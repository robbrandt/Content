import { GridStackNode, GridStackWidget } from './types';
export declare type onChangeCB = (nodes: GridStackNode[], removeDOM?: boolean) => void;
/**
 * Defines the GridStack engine that does most no DOM grid manipulation.
 * See GridStack methods and vars for descriptions.
 *
 * NOTE: values should not be modified directly - call the main GridStack API instead
 */
export declare class GridStackEngine {
    column: number;
    maxRow: number;
    nodes: GridStackNode[];
    onchange: onChangeCB;
    addedNodes: GridStackNode[];
    removedNodes: GridStackNode[];
    batchMode: boolean;
    constructor(column?: number, onchange?: onChangeCB, float?: boolean, maxRow?: number, nodes?: GridStackNode[]);
    batchUpdate(): GridStackEngine;
    commit(): GridStackEngine;
    isAreaEmpty(x: number, y: number, width: number, height: number): boolean;
    /** re-layout grid items to reclaim any empty space */
    compact(): GridStackEngine;
    /** enable/disable floating widgets (default: `false`) See [example](http://gridstackjs.com/demo/float.html) */
    /** float getter method */
    float: boolean;
    /**
     * given a random node, makes sure it's coordinates/values are valid in the current grid
     * @param node to adjust
     * @param resizing if out of bound, resize down or move into the grid to fit ?
     */
    prepareNode(node: GridStackNode, resizing?: boolean): GridStackNode;
    getDirtyNodes(verify?: boolean): GridStackNode[];
    cleanNodes(): GridStackEngine;
    addNode(node: GridStackNode, triggerAddEvent?: boolean): GridStackNode;
    removeNode(node: GridStackNode, removeDOM?: boolean, triggerEvent?: boolean): GridStackEngine;
    removeAll(removeDOM?: boolean): GridStackEngine;
    canMoveNode(node: GridStackNode, x: number, y: number, width?: number, height?: number): boolean;
    canBePlacedWithRespectToHeight(node: GridStackNode): boolean;
    isNodeChangedPosition(node: GridStackNode, x: number, y: number, width: number, height: number): boolean;
    moveNode(node: GridStackNode, x: number, y: number, width?: number, height?: number, noPack?: boolean): GridStackNode;
    getRow(): number;
    beginUpdate(node: GridStackNode): GridStackEngine;
    endUpdate(): GridStackEngine;
    /** saves the current layout returning a list of widgets for serialization */
    save(): GridStackWidget[];
    /** called to remove all internal values */
    cleanupNode(node: GridStackNode): void;
}