/**
 * Imagine Page Builder - Editor Script
 * 
 * This file contains the main Vue.js application that powers the Imagine editor.
 */

// Import required dependencies
import Vue from 'vue';

// Main Editor Component
const ImagineEditor = {
  data() {
    return {
      blocks: [],
      availableBlocks: window.imagineData.available_blocks || {},
      selectedBlock: null,
      isDragging: false,
      draggedBlock: null,
      dropTarget: null,
      isLoading: false,
      hasChanges: false,
      post: {
        id: window.imagineData.post_id,
        title: window.imagineData.post_title,
        content: window.imagineData.post_content,
      }
    };
  },
  
  mounted() {
    // Load existing blocks if available
    this.loadBlocks();
    
    // Set up drag events for the document
    document.addEventListener('mouseup', this.handleDocumentMouseUp);
    
    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', (event) => {
      if (this.hasChanges) {
        event.preventDefault();
        event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return event.returnValue;
      }
    });
  },
  
  beforeDestroy() {
    document.removeEventListener('mouseup', this.handleDocumentMouseUp);
  },
  
  methods: {
    loadBlocks() {
      try {
        const savedBlocks = JSON.parse(window.imagineData.blocks_data || '[]');
        this.blocks = savedBlocks.map((block, index) => ({
          ...block,
          id: block.id || `block-${Date.now()}-${index}`
        }));
      } catch (e) {
        console.error('Error loading blocks:', e);
        this.blocks = [];
      }
    },
    
    startDrag(blockType) {
      this.isDragging = true;
      this.draggedBlock = {
        type: blockType,
        ...this.availableBlocks[blockType]
      };
      
      // Create a visual representation of the dragged element
      const dragImage = document.createElement('div');
      dragImage.classList.add('imagine-dragged-block');
      dragImage.innerText = this.availableBlocks[blockType].name;
      document.body.appendChild(dragImage);
      
      // Update the drag image position with mouse movement
      document.addEventListener('mousemove', this.handleDragMove);
    },
    
    handleDragMove(event) {
      if (!this.isDragging) return;
      
      const dragImage = document.querySelector('.imagine-dragged-block');
      if (dragImage) {
        dragImage.style.left = `${event.clientX + 10}px`;
        dragImage.style.top = `${event.clientY + 10}px`;
      }
      
      // Find the potential drop target
      this.findDropTarget(event);
    },
    
    findDropTarget(event) {
      // Reset current drop target indicator
      document.querySelectorAll('.imagine-drop-indicator').forEach(el => el.remove());
      
      const canvas = document.querySelector('.imagine-editor-canvas');
      if (!canvas) return;
      
      // Check if we're over the canvas
      const canvasRect = canvas.getBoundingClientRect();
      if (
        event.clientX >= canvasRect.left &&
        event.clientX <= canvasRect.right &&
        event.clientY >= canvasRect.top &&
        event.clientY <= canvasRect.bottom
      ) {
        // Find the closest block to insert before/after
        const blockElements = Array.from(document.querySelectorAll('.imagine-block'));
        
        if (blockElements.length === 0) {
          // No blocks yet, indicate drop at the beginning
          this.createDropIndicator(canvas, 'inside-top');
          this.dropTarget = { position: 'start', index: 0 };
          return;
        }
        
        // Find the closest block
        let closestBlock = null;
        let closestDistance = Infinity;
        let closestPosition = '';
        let closestIndex = 0;
        
        blockElements.forEach((block, index) => {
          const rect = block.getBoundingClientRect();
          const blockMiddleY = rect.top + (rect.height / 2);
          
          // Check if we're above the block
          if (event.clientY < blockMiddleY) {
            const distance = Math.abs(event.clientY - rect.top);
            if (distance < closestDistance) {
              closestDistance = distance;
              closestBlock = block;
              closestPosition = 'before';
              closestIndex = index;
            }
          } 
          // We're below the block
          else {
            const distance = Math.abs(event.clientY - rect.bottom);
            if (distance < closestDistance) {
              closestDistance = distance;
              closestBlock = block;
              closestPosition = 'after';
              closestIndex = index + 1;
            }
          }
        });
        
        if (closestBlock) {
          this.createDropIndicator(closestBlock, closestPosition);
          this.dropTarget = { 
            position: closestPosition, 
            index: closestIndex, 
            blockId: closestBlock.dataset.blockId 
          };
        }
      } else {
        this.dropTarget = null;
      }
    },
    
    createDropIndicator(element, position) {
      const indicator = document.createElement('div');
      indicator.classList.add('imagine-drop-indicator');
      
      if (position === 'before') {
        indicator.style.top = `${element.offsetTop - 5}px`;
      } else if (position === 'after') {
        indicator.style.top = `${element.offsetTop + element.offsetHeight}px`;
      } else if (position === 'inside-top') {
        indicator.style.top = '10px';
      }
      
      element.parentElement.appendChild(indicator);
    },
    
    handleDocumentMouseUp() {
      if (this.isDragging) {
        this.isDragging = false;
        
        // Remove drag image
        const dragImage = document.querySelector('.imagine-dragged-block');
        if (dragImage) {
          dragImage.remove();
        }
        
        // Remove drop indicators
        document.querySelectorAll('.imagine-drop-indicator').forEach(el => el.remove());
        
        // Clean up event listener
        document.removeEventListener('mousemove', this.handleDragMove);
        
        // Handle drop if we have a valid target
        if (this.dropTarget && this.draggedBlock) {
          this.handleDrop();
        }
        
        this.draggedBlock = null;
        this.dropTarget = null;
      }
    },
    
    handleDrop() {
      // Create a new block based on the dragged type
      const newBlock = {
        id: `block-${Date.now()}`,
        type: this.draggedBlock.type,
        content: this.draggedBlock.defaults?.content || '',
        settings: { ...this.draggedBlock.defaults }
      };
      
      // Insert at the target position
      const newBlocks = [...this.blocks];
      newBlocks.splice(this.dropTarget.index, 0, newBlock);
      this.blocks = newBlocks;
      
      // Mark that we have unsaved changes
      this.hasChanges = true;
      
      // Select the newly added block
      this.$nextTick(() => {
        this.selectBlock(newBlock.id);
      });
    },
    
    selectBlock(blockId) {
      this.selectedBlock = this.blocks.find(block => block.id === blockId) || null;
    },
    
    updateBlockContent(blockId, content) {
      const index = this.blocks.findIndex(block => block.id === blockId);
      if (index !== -1) {
        const updatedBlock = { ...this.blocks[index], content };
        this.blocks.splice(index, 1, updatedBlock);
        if (this.selectedBlock && this.selectedBlock.id === blockId) {
          this.selectedBlock = updatedBlock;
        }
        this.hasChanges = true;
      }
    },
    
    updateBlockSettings(blockId, settings) {
      const index = this.blocks.findIndex(block => block.id === blockId);
      if (index !== -1) {
        const updatedBlock = { 
          ...this.blocks[index], 
          settings: { ...this.blocks[index].settings, ...settings }
        };
        this.blocks.splice(index, 1, updatedBlock);
        if (this.selectedBlock && this.selectedBlock.id === blockId) {
          this.selectedBlock = updatedBlock;
        }
        this.hasChanges = true;
      }
    },
    
    removeBlock(blockId) {
      const index = this.blocks.findIndex(block => block.id === blockId);
      if (index !== -1) {
        this.blocks.splice(index, 1);
        if (this.selectedBlock && this.selectedBlock.id === blockId) {
          this.selectedBlock = null;
        }
        this.hasChanges = true;
      }
    },
    
    moveBlockUp(blockId) {
      const index = this.blocks.findIndex(block => block.id === blockId);
      if (index > 0) {
        const block = this.blocks[index];
        this.blocks.splice(index, 1);
        this.blocks.splice(index - 1, 0, block);
        this.hasChanges = true;
      }
    },
    
    moveBlockDown(blockId) {
      const index = this.blocks.findIndex(block => block.id === blockId);
      if (index !== -1 && index < this.blocks.length - 1) {
        const block = this.blocks[index];
        this.blocks.splice(index, 1);
        this.blocks.splice(index + 1, 0, block);
        this.hasChanges = true;
      }
    },
    
    saveChanges() {
      this.isLoading = true;
      
      // Generate content from blocks
      const htmlContent = this.generateHtmlContent();
      
      // Prepare the data to send
      const data = new FormData();
      data.append('action', 'imagine_save_page_data');
      data.append('nonce', window.imagineData.nonce);
      data.append('post_id', this.post.id);
      data.append('content', htmlContent);
      data.append('blocks_data', JSON.stringify(this.blocks));
      
      // Send to server
      fetch(window.imagineData.ajax_url, {
        method: 'POST',
        body: data,
        credentials: 'same-origin'
      })
      .then(response => response.json())
      .then(result => {
        this.isLoading = false;
        if (result.success) {
          this.hasChanges = false;
          // Show success notification
          this.showNotification('Changes saved successfully', 'success');
        } else {
          // Show error notification
          this.showNotification('Error saving changes', 'error');
        }
      })
      .catch(error => {
        this.isLoading = false;
        console.error('Error saving page:', error);
        this.showNotification('Error saving changes', 'error');
      });
    },
    
    generateHtmlContent() {
      // Basic HTML generation from blocks - this can be expanded based on your needs
      return this.blocks.map(block => {
        switch (block.type) {
          case 'header':
            const tag = block.settings?.level || 'h2';
            return `<${tag}>${block.content || ''}</${tag}>`;
          case 'paragraph':
            return `<p>${block.content || ''}</p>`;
          default:
            return '';
        }
      }).join('\n\n');
    },
    
    showNotification(message, type = 'info') {
      // Create a notification element
      const notification = document.createElement('div');
      notification.classList.add('imagine-notification', `imagine-notification-${type}`);
      notification.innerText = message;
      
      // Add to document
      document.body.appendChild(notification);
      
      // Remove after a delay
      setTimeout(() => {
        notification.classList.add('imagine-notification-hiding');
        setTimeout(() => notification.remove(), 500);
      }, 3000);
    }
  },
  
  template: `
    <div class="imagine-editor">
      <div class="imagine-header">
        <div class="imagine-logo">Imagine Page Builder</div>
        <div class="imagine-actions">
          <button 
            class="imagine-button imagine-button-primary" 
            @click="saveChanges" 
            :disabled="isLoading || !hasChanges"
          >
            {{ isLoading ? 'Saving...' : 'Save Changes' }}
          </button>
          <a 
            class="imagine-button" 
            :href="'post.php?post=' + post.id + '&action=edit'"
          >
            Exit to WordPress Editor
          </a>
        </div>
      </div>
      
      <div class="imagine-main">
        <div class="imagine-blocks-sidebar">
          <h3>Available Blocks</h3>
          <div class="imagine-block-picker">
            <div 
              v-for="(block, type) in availableBlocks" 
              :key="type"
              class="imagine-block-item"
              @mousedown.prevent="startDrag(type)"
            >
              <div class="imagine-block-item-icon">
                <span class="dashicons" :class="'dashicons-' + block.icon"></span>
              </div>
              <div class="imagine-block-item-name">{{ block.name }}</div>
            </div>
          </div>
        </div>
        
        <div class="imagine-editor-canvas">
          <h2 v-if="blocks.length === 0" class="imagine-empty-canvas">
            Drag blocks from the sidebar to start building your page
          </h2>
          
          <div 
            v-for="block in blocks" 
            :key="block.id" 
            class="imagine-block" 
            :class="{'imagine-block-selected': selectedBlock && selectedBlock.id === block.id}"
            :data-block-id="block.id"
            @click="selectBlock(block.id)"
          >
            <div class="imagine-block-content">
              <component 
                :is="'imagine-' + block.type" 
                :block="block"
                @update:content="updateBlockContent(block.id, $event)"
                @update:settings="updateBlockSettings(block.id, $event)"
              >
                <!-- Fallback content if component not defined -->
                <div v-if="block.type === 'header'">
                  <component :is="block.settings?.level || 'h2'">{{ block.content }}</component>
                </div>
                <div v-else-if="block.type === 'paragraph'" class="imagine-paragraph">
                  {{ block.content }}
                </div>
                <div v-else>
                  Unknown block type: {{ block.type }}
                </div>
              </component>
            </div>
            
            <div class="imagine-block-actions">
              <button @click.stop="moveBlockUp(block.id)" :disabled="blocks.indexOf(block) === 0">
                <span class="dashicons dashicons-arrow-up-alt"></span>
              </button>
              <button @click.stop="moveBlockDown(block.id)" :disabled="blocks.indexOf(block) === blocks.length - 1">
                <span class="dashicons dashicons-arrow-down-alt"></span>
              </button>
              <button @click.stop="removeBlock(block.id)">
                <span class="dashicons dashicons-trash"></span>
              </button>
            </div>
          </div>
        </div>
        
        <div class="imagine-sidebar imagine-settings-sidebar">
          <h3>Block Settings</h3>
          <div v-if="selectedBlock" class="imagine-block-settings">
            <h4>{{ availableBlocks[selectedBlock.type]?.name || 'Block' }} Settings</h4>
            
            <!-- Common settings for all blocks -->
            <div class="imagine-setting">
              <label>Content</label>
              <textarea 
                v-model="selectedBlock.content" 
                @input="updateBlockContent(selectedBlock.id, selectedBlock.content)"
              ></textarea>
            </div>
            
            <!-- Header-specific settings -->
            <div v-if="selectedBlock.type === 'header'" class="imagine-setting">
              <label>Heading Level</label>
              <select 
                v-model="selectedBlock.settings.level"
                @change="updateBlockSettings(selectedBlock.id, { level: selectedBlock.settings.level })"
              >
                <option value="h1">H1</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
                <option value="h4">H4</option>
                <option value="h5">H5</option>
                <option value="h6">H6</option>
              </select>
            </div>
            
            <!-- Add more block-specific settings here -->
          </div>
          <div v-else class="imagine-no-selection">
            Select a block to edit its settings
          </div>
        </div>
      </div>
    </div>
  `
};

// Register block components
Vue.component('imagine-header', {
  props: ['block'],
  template: `
    <component :is="block.settings?.level || 'h2'" contenteditable="true"
      @input="$emit('update:content', $event.target.innerText)"
    >{{ block.content }}</component>
  `
});

Vue.component('imagine-paragraph', {
  props: ['block'],
  template: `
    <p contenteditable="true"
      @input="$emit('update:content', $event.target.innerText)"
    >{{ block.content }}</p>
  `
});

// Initialize the Vue app when the DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const app = new Vue({
    el: '#imagine-editor-app',
    render: h => h(ImagineEditor)
  });
});
