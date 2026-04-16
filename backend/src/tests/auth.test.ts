import assert from 'node:assert/strict';
import test from 'node:test';
import { createApp } from '../app.js';

test('health endpoint should return ok', async () => {
  const app = createApp();
  const server = app.listen(0);
  const port = (server.address() as { port: number }).port;

  const response = await fetch(`http://127.0.0.1:${port}/health`);
  const payload = await response.json();

  assert.equal(response.status, 200);
  assert.equal(payload.status, 'ok');
  server.close();
});
