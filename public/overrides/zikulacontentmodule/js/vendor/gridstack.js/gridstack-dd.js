"use strict";
// gridstack-dd.ts 2.2.0 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
exports.GridStackDD = void 0;
/**
 * Base class for drag'n'drop plugin.
 */
class GridStackDD {
    constructor(grid) {
        this.grid = grid;
    }
    /** call this method to register your plugin instead of the default no-op one */
    static registerPlugin(pluginClass) {
        GridStackDD.registeredPlugins = pluginClass;
    }
    /** get the current registered plugin to use */
    static get() {
        return GridStackDD.registeredPlugins || GridStackDD;
    }
    /** removes any drag&drop present (called during destroy) */
    remove(el) {
        this.draggable(el, 'destroy').resizable(el, 'destroy');
        if (el.gridstackNode) {
            delete el.gridstackNode._initDD; // reset our DD init flag
        }
        return this;
    }
    resizable(el, opts, key, value) {
        return this;
    }
    draggable(el, opts, key, value) {
        return this;
    }
    dragIn(el, opts) {
        return this;
    }
    isDraggable(el) {
        return false;
    }
    droppable(el, opts, key, value) {
        return this;
    }
    isDroppable(el) {
        return false;
    }
    on(el, eventName, callback) {
        return this;
    }
    off(el, eventName) {
        return this;
    }
}
exports.GridStackDD = GridStackDD;
//# sourceMappingURL=gridstack-dd.js.map