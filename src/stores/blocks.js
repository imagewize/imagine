/**
 * Blocks Store Module
 * 
 * This Pinia store manages the state and operations for the page builder blocks.
 * It handles the creation, selection, updating, and deletion of blocks in the editor.
 * 
 * @module src/stores/blocks
 * 
 * State:
 * - blocks: Array of block objects representing the content in the editor
 * - selectedBlockIndex: Currently selected block index for editing
 * 
 * Getters:
 * - selectedBlock: Returns the currently selected block object
 * - blockCount: Returns the total number of blocks in the editor
 * 
 * Actions:
 * - addBlock: Adds a new block of specified type with default properties
 * - updateBlock: Updates an existing block at a specific index
 * - deleteBlock: Removes a block at a specific index
 * - selectBlock: Sets the selected block index
 * - setBlocks: Replaces all blocks with a new array
 * - moveBlock: Moves a block from one position to another
 */
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useBlocksStore = defineStore('blocks', () => {
  // State
  const blocks = ref([]);
  const selectedBlockIndex = ref(null);

  // Getters
  const selectedBlock = computed(() => {
    if (selectedBlockIndex.value !== null && blocks.value[selectedBlockIndex.value]) {
      return blocks.value[selectedBlockIndex.value];
    }
    return null;
  });

  const blockCount = computed(() => blocks.value.length);

  // Actions
  function addBlock(type, defaults = {}) {
    const newBlock = {
      id: `block-${Date.now()}`,
      type,
      content: defaults.content || '',
      settings: { ...defaults }
    };
    
    blocks.value.push(newBlock);
    selectedBlockIndex.value = blocks.value.length - 1;
  }
  
  function updateBlock(index, updatedBlock) {
    if (index >= 0 && index < blocks.value.length) {
      blocks.value[index] = updatedBlock;
    }
  }
  
  function deleteBlock(index) {
    if (index >= 0 && index < blocks.value.length) {
      blocks.value.splice(index, 1);
      
      if (selectedBlockIndex.value === index) {
        selectedBlockIndex.value = null;
      } else if (selectedBlockIndex.value > index) {
        selectedBlockIndex.value--;
      }
    }
  }
  
  function selectBlock(index) {
    if (index >= 0 && index < blocks.value.length) {
      selectedBlockIndex.value = index;
    } else {
      selectedBlockIndex.value = null;
    }
  }
  
  function setBlocks(newBlocks) {
    blocks.value = newBlocks;
  }
  
  function moveBlock(fromIndex, toIndex) {
    if (
      fromIndex >= 0 && 
      fromIndex < blocks.value.length && 
      toIndex >= 0 && 
      toIndex < blocks.value.length
    ) {
      const block = blocks.value.splice(fromIndex, 1)[0];
      blocks.value.splice(toIndex, 0, block);
      
      // Update selected index if needed
      if (selectedBlockIndex.value === fromIndex) {
        selectedBlockIndex.value = toIndex;
      }
    }
  }

  return { 
    blocks, 
    selectedBlockIndex,
    selectedBlock,
    blockCount,
    addBlock,
    updateBlock,
    deleteBlock,
    selectBlock,
    setBlocks,
    moveBlock
  };
});
