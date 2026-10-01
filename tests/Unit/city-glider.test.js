import assert from 'node:assert/strict';
import { test } from 'node:test';
import { createGlider } from '../../resources/js/city-glider.js';

test('the glider crosses the city, waits out of view, and returns facing the other way', () => {
    const glider = createGlider();
    const update = glider.userData.update;

    for (const time of [0, 4.99, 19, 37.99, 38, 42.99, 57]) {
        update(time);
        assert.equal(glider.visible, false, `hidden at ${time}s`);
    }

    update(5);
    assert.equal(glider.visible, true);
    assert.ok(glider.position.x < -200);
    update(12);
    assert.equal(glider.visible, true);
    assert.equal(glider.position.x, 0);
    assert.ok(glider.position.y > 120);
    update(18.99);
    assert.ok(glider.position.x > 200);
    assert.ok(glider.getWorldDirection(glider.position.clone()).z > 0);

    update(43);
    assert.equal(glider.visible, true);
    assert.ok(glider.position.x > 200);
    assert.ok(glider.getWorldDirection(glider.position.clone()).z < 0);
    update(56.99);
    assert.ok(glider.position.x < -200);

    update(81);
    assert.equal(glider.visible, true);
    assert.ok(glider.position.x < -200);
});
