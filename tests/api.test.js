/**
 * Integration Tests — Sports Portal REST API
 * TC-7.1 … TC-7.7
 *
 * Ендпоінт: http://localhost/sports/wp-json/wp/v2/
 * Запуск:   npm test
 */

const axios = require('axios');

const BASE = 'http://localhost/sports/wp-json/wp/v2';

// ─── TC-7.1 ──────────────────────────────────────────────────────────────────
test('TC-7.1: GET /event returns 200 and array', async () => {
  const res = await axios.get(`${BASE}/event`);
  expect(res.status).toBe(200);
  expect(Array.isArray(res.data)).toBe(true);
  expect(res.data.length).toBeGreaterThan(0);
});

// ─── TC-7.2 ──────────────────────────────────────────────────────────────────
test('TC-7.2: GET /event/1001 returns single event with required fields', async () => {
  const res = await axios.get(`${BASE}/event/1001`);
  expect(res.status).toBe(200);
  expect(res.data.id).toBe(1001);
  expect(res.data).toHaveProperty('title');
  expect(res.data).toHaveProperty('slug');
  expect(res.data).toHaveProperty('status');
  expect(res.data.type).toBe('event');
});

// ─── TC-7.3 ──────────────────────────────────────────────────────────────────
test('TC-7.3: GET /event?per_page=3 returns max 3 events', async () => {
  const res = await axios.get(`${BASE}/event?per_page=3`);
  expect(res.status).toBe(200);
  expect(res.data.length).toBeLessThanOrEqual(3);
});

// ─── TC-7.4 ──────────────────────────────────────────────────────────────────
test('TC-7.4: GET /schedule returns 200 and array', async () => {
  const res = await axios.get(`${BASE}/schedule`);
  expect(res.status).toBe(200);
  expect(Array.isArray(res.data)).toBe(true);
});

// ─── TC-7.5 ──────────────────────────────────────────────────────────────────
test('TC-7.5: GET /results returns 200 and array', async () => {
  const res = await axios.get(`${BASE}/results`);
  expect(res.status).toBe(200);
  expect(Array.isArray(res.data)).toBe(true);
});

// ─── TC-7.6 ──────────────────────────────────────────────────────────────────
test('TC-7.6: GET /news returns 200 and array', async () => {
  const res = await axios.get(`${BASE}/news`);
  expect(res.status).toBe(200);
  expect(Array.isArray(res.data)).toBe(true);
});

// ─── TC-7.7 ──────────────────────────────────────────────────────────────────
test('TC-7.7: GET /event/9999 returns 404', async () => {
  try {
    await axios.get(`${BASE}/event/9999`);
    throw new Error('Expected 404 but got 2xx');
  } catch (err) {
    expect(err.response.status).toBe(404);
  }
});