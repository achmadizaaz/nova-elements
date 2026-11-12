import assert from 'node:assert/strict';

// Requires Node 22+ and a disposable Chrome session with loopback CDP enabled.
const [carouselUrl, listUrl, debuggerUrl = 'http://127.0.0.1:9228'] = process.argv.slice(2);
if (!carouselUrl || !listUrl) {
  throw new Error('Usage: node tests/browser-blog-carousel.mjs <two-carousel-page> <list-only-page> [chrome-debugger-url]');
}

const target = await (await fetch(new URL('/json/new?about:blank', debuggerUrl), { method: 'PUT' })).json();
const socket = new WebSocket(target.webSocketDebuggerUrl);
await new Promise((resolve, reject) => {
  socket.addEventListener('open', resolve, { once: true });
  socket.addEventListener('error', reject, { once: true });
});

let sequence = 0;
const pending = new Map();
socket.addEventListener('message', ({ data }) => {
  const message = JSON.parse(data);
  const callback = pending.get(message.id);
  if (!callback) return;
  pending.delete(message.id);
  clearTimeout(callback.timer);
  if (message.error) callback.reject(new Error(message.error.message));
  else callback.resolve(message.result);
});

function cdp(method, params = {}) {
  return new Promise((resolve, reject) => {
    const id = ++sequence;
    const timer = setTimeout(() => {
      pending.delete(id);
      reject(new Error(method + ' timed out'));
    }, 20000);
    pending.set(id, { resolve, reject, timer });
    socket.send(JSON.stringify({ id, method, params }));
  });
}

async function evaluate(expression) {
  const result = await cdp('Runtime.evaluate', { expression, returnByValue: true });
  if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
  return result.result.value;
}

const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function waitFor(expression, description, max = 15000) {
  const deadline = Date.now() + max;
  while (Date.now() < deadline) {
    try {
      if (await evaluate(expression)) return;
    } catch (error) {
      if (!/context|Frame was detached/i.test(error.message)) throw error;
    }
    await delay(150);
  }
  throw new Error('Timed out: ' + description);
}

async function navigate(url, selector) {
  await cdp('Page.navigate', { url });
  await waitFor('location.href === ' + JSON.stringify(url) + ' && document.readyState === "complete" && !!document.querySelector(' + JSON.stringify(selector) + ')', url, 25000);
}

async function press(key, code, windowsVirtualKeyCode) {
  await cdp('Input.dispatchKeyEvent', { type: 'keyDown', key, code, windowsVirtualKeyCode, text: key === 'Enter' ? '\r' : key });
  await cdp('Input.dispatchKeyEvent', { type: 'keyUp', key, code, windowsVirtualKeyCode });
}

const first = 'document.querySelectorAll("nova-post-carousel")[0]';
const second = 'document.querySelectorAll("nova-post-carousel")[1]';
const viewport = (element) => element + '.querySelector(".nova-blog__grid")';
const setMotion = (value) => cdp('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value }] });

try {
  await cdp('Page.enable');
  await cdp('Page.bringToFront');
  await cdp('Emulation.setFocusEmulationEnabled', { enabled: true });
  await cdp('Emulation.setDeviceMetricsOverride', { width: 1240, height: 900, deviceScaleFactor: 1, mobile: false });
  await setMotion('no-preference');
  await cdp('Input.dispatchMouseEvent', { type: 'mouseMoved', x: 0, y: 0 });
  await navigate(carouselUrl, 'nova-post-carousel');
  await waitFor('!!(' + second + ')', 'two carousel instances');
  await waitFor(viewport(first) + '.scrollLeft > 50', 'desktop autoplay', 10000);
  await evaluate(first + '.querySelector(\'[data-step="1"]\').focus()');
  await waitFor('(() => { const rotation = ' + first + '.querySelector("[data-rotation]"); return rotation.textContent === rotation.dataset.play; })()', 'focus stops rotation');

  await delay(600);
  const firstOffset = await evaluate(viewport(first) + '.scrollLeft');
  const secondOffset = await evaluate(viewport(second) + '.scrollLeft');
  await press(' ', 'Space', 32);
  await waitFor(viewport(first) + '.scrollLeft > ' + (firstOffset + 50), 'Space activates Next');
  assert.equal(await evaluate(viewport(second) + '.scrollLeft'), secondOffset);

  await delay(500);
  await evaluate(first + '.querySelector(\'[data-page="0"]\').focus()');
  await press('Enter', 'Enter', 13);
  await waitFor(viewport(first) + '.scrollLeft < 2 && ' + first + '.querySelector(\'[data-page="0"]\').getAttribute("aria-current") === "true"', 'Enter selects first slide page');
  await setMotion('reduce');
  await waitFor(first + '.querySelector("[data-rotation]").hidden', 'reduced motion suppresses rotation');
  await delay(4400);
  assert.equal(await evaluate(viewport(first) + '.scrollLeft'), 0);

  await navigate(listUrl, '.nova-blog--list');
  assert.equal(await evaluate('!!document.querySelector("link[href*=blog-collection]")'), true);
  assert.equal(await evaluate('!!document.querySelector("script[src*=blog-carousel]")'), false);
  console.log('PASS: rotation, keyboard focus, Space and Enter, instance isolation, reduced motion, and conditional assets');
} finally {
  socket.close();
  await fetch(new URL('/json/close/' + target.id, debuggerUrl));
}
