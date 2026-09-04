import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(new URL('../../resources/js/project-accordion.js', import.meta.url), 'utf8');
function setup() {
    const events = () => ({ listeners: {}, addEventListener(name, fn) { this.listeners[name] = fn; } });
    const classes = () => ({ values: new Set(), add(v) { this.values.add(v); }, remove(v) { this.values.delete(v); }, toggle(v, on) { on ? this.add(v) : this.remove(v); } });
    const panels = Array.from({ length: 4 }, () => ({
        ...events(), classList: classes(), contains() { return false; },
        slides: Array.from({ length: 2 }, (_, i) => ({ complete: true, naturalWidth: 400, classList: Object.assign(classes(), { values: new Set(i ? [] : ['is-active']) }) })),
        querySelectorAll() { return this.slides; },
    }));
    const container = { ...events(), querySelectorAll: () => panels, contains: () => false };
    const document = { ...events(), hidden: false, getElementById: () => container };
    const desktop = { ...events(), matches: true }, reduced = { ...events(), matches: false };
    const timers = new Map();
    let id = 0, observer;
    const context = { document, window: events(), matchMedia: q => q.includes('reduced') ? reduced : desktop,
        setTimeout: fn => { timers.set(++id, fn); return id; }, clearTimeout: id => timers.delete(id),
        IntersectionObserver: class { constructor(fn) { observer = fn; } observe() {} } };
    context.window.IntersectionObserver = context.IntersectionObserver;
    vm.runInNewContext(source, context);
    document.listeners.DOMContentLoaded();
    return { panels, container, document, desktop, reduced, timers,
        visible(on) { observer([{ isIntersecting: on }]); },
        tick() { const [id, fn] = [...timers][0]; timers.delete(id); fn(); } };
}

test('one timer advances one panel at a time', () => {
    const s = setup();
    assert.equal(s.timers.size, 0);
    s.visible(true);
    assert.equal(s.timers.size, 1);
    s.tick();
    assert.equal(s.panels.filter(p => p.slides[1].classList.values.has('is-active')).length, 1);
    assert.equal(s.timers.size, 1);
    s.tick();
    assert.equal(s.panels.filter(p => p.slides[1].classList.values.has('is-active')).length, 2);
});

test('expanded, hidden, offscreen, touch/mobile and reduced motion stop scheduling', () => {
    const s = setup(); s.visible(true);
    s.panels[0].listeners.focusin(); assert.equal(s.timers.size, 0);
    s.container.listeners.focusout({ relatedTarget: null }); assert.equal(s.timers.size, 1);
    s.document.hidden = true; s.document.listeners.visibilitychange(); assert.equal(s.timers.size, 0);
    s.document.hidden = false; s.document.listeners.visibilitychange();
    s.visible(false); assert.equal(s.timers.size, 0);
    s.visible(true); s.desktop.matches = false; s.desktop.listeners.change(); assert.equal(s.timers.size, 0);
    s.desktop.matches = true; s.desktop.listeners.change();
    s.reduced.matches = true; s.reduced.listeners.change(); assert.equal(s.timers.size, 0);
});

test('unloaded slide never replaces the current visual', () => {
    const s = setup(); s.panels[0].slides[1].naturalWidth = 0;
    s.visible(true); s.tick();
    assert.ok(s.panels[0].slides[0].classList.values.has('is-active'));
});
