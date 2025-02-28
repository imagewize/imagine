<template>
  <div class="imagine-editor">
    <div class="imagine-editor__header">
      <h1>Imagine Editor: {{ postTitle }}</h1>
      <div class="imagine-editor__actions">
        <button class="imagine-button imagine-button--secondary" @click="backToWordPress">
          Cancel
        </button>
        <button class="imagine-button imagine-button--primary" @click="saveContent">
          Save Changes
        </button>
      </div>
    </div>
    <div class="imagine-editor__body">
      <div class="imagine-editor__sidebar">
        <h2>Blocks</h2>
        <div class="imagine-blocks-list">
          <div 
            v-for="(block, type) in availableBlocks" 
            :key="type"
            class="imagine-block-item"
            @click="addBlock(type, block.defaults)"
          >
            <div class="imagine-block-item__icon">
              <span :class="`dashicons dashicons-${block.icon}`"></span>
            </div>
            <div class="imagine-block-item__name">{{ block.name }}</div>
          </div>
        </div>
      </div>
      <div class="imagine-editor__content">
        <EditorCanvas 
          :blocks="blocks"
          @update:blocks="updateBlocks"
        />
      </div>
      <div class="imagine-editor__options">
        <BlockOptions 
          v-if="selectedBlock !== null"
          :block="blocks[selectedBlock]"
          @update:block="updateSelectedBlock"
        />
        <div v-else class="imagine-empty-state">
          Select a block to edit its options
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import EditorCanvas from './components/EditorCanvas.vue';
import BlockOptions from './components/BlockOptions.vue';
import { useBlocksStore } from './stores/blocks';

export default {
  components: {
    EditorCanvas,
    BlockOptions
  },
  setup() {
    const blocksStore = useBlocksStore();
    const postId = ref(0);
    const postTitle = ref('');
    const blocks = ref([]);
    const selectedBlock = ref(null);
    
    const availableBlocks = reactive({
      header: {
        name: 'Header',
        icon: 'heading',
        defaults: {
          level: 'h2',
          content: 'Header Text'
        }
      },
      paragraph: {
        name: 'Paragraph',
        icon: 'text',
        defaults: {
          content: 'Enter your text here...'
        }
      }
    });

    onMounted(() => {
      // Get data from WordPress
      if (window.imagineData) {
        postId.value = window.imagineData.post_id;
        postTitle.value = window.imagineData.post_title;
        
        // Load blocks data if available
        if (window.imagineData.blocks_data) {
          try {
            const blocksData = JSON.parse(window.imagineData.blocks_data);
            blocks.value = blocksData;
          } catch (e) {
            console.error('Failed to parse blocks data', e);
          }
        }
      }
    });

    const addBlock = (type, defaults) => {
      const newBlock = {
        id: `block-${Date.now()}`,
        type,
        content: defaults.content || '',
        settings: { ...defaults }
      };
      
      blocks.value.push(newBlock);
      selectedBlock.value = blocks.value.length - 1;
    };

    const updateBlocks = (newBlocks) => {
      blocks.value = newBlocks;
    };

    const updateSelectedBlock = (updatedBlock) => {
      if (selectedBlock.value !== null) {
        blocks.value[selectedBlock.value] = updatedBlock;
      }
    };

    const saveContent = () => {
      // Convert blocks to HTML content
      let htmlContent = '';
      blocks.value.forEach(block => {
        switch (block.type) {
          case 'header':
            const tag = block.settings.level || 'h2';
            htmlContent += `<${tag}>${block.content}</${tag}>`;
            break;
          case 'paragraph':
            htmlContent += `<p>${block.content}</p>`;
            break;
          default:
            break;
        }
      });

      // Save via AJAX
      const formData = new FormData();
      formData.append('action', 'imagine_save_page_data');
      formData.append('post_id', postId.value);
      formData.append('content', htmlContent);
      formData.append('blocks_data', JSON.stringify(blocks.value));
      formData.append('nonce', window.imagineData.nonce);

      fetch(window.imagineData.ajax_url, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Page saved successfully!');
        } else {
          alert('Error saving page.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error saving page.');
      });
    };

    const backToWordPress = () => {
      window.history.back();
    };

    return {
      postId,
      postTitle,
      blocks,
      selectedBlock,
      availableBlocks,
      addBlock,
      updateBlocks,
      updateSelectedBlock,
      saveContent,
      backToWordPress
    };
  }
};
</script>

<style lang="scss">
/* Modern Sass usage - no @use "sass:color" since it's in Vite config */
@use "@/styles/_variables.scss" as vars;

.imagine-editor {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: vars.$color-background;
  color: vars.$color-text;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  
  &__header {
    padding: vars.$spacing-sm vars.$spacing-lg;
    background-color: vars.$color-white;
    border-bottom: 1px solid vars.$color-border;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: vars.$shadow-sm;
  }
  
  &__body {
    display: flex;
    flex: 1;
    overflow: hidden;
  }
  
  &__sidebar {
    width: vars.$sidebar-width;
    background-color: vars.$color-white;
    padding: vars.$spacing-md;
    border-right: 1px solid vars.$color-border;
    overflow-y: auto;
  }
  
  &__content {
    flex: 1;
    padding: vars.$spacing-lg;
    overflow-y: auto;
  }
  
  &__options {
    width: vars.$options-panel-width;
    background-color: vars.$color-white;
    border-left: 1px solid vars.$color-border;
    padding: vars.$spacing-md;
    overflow-y: auto;
  }
}

.imagine-blocks-list {
  margin-top: vars.$spacing-md;
}

.imagine-block-item {
  display: flex;
  padding: vars.$spacing-sm vars.$spacing-md;
  background-color: #f9f9f9;
  border: 1px solid vars.$color-border;
  border-radius: vars.$border-radius-md;
  margin-bottom: vars.$spacing-sm;
  cursor: pointer;
  transition: all vars.$transition-speed;
  
  &:hover {
    background-color: #efefef;
    border-color: vars.$color-primary;
  }
  
  &__icon {
    margin-right: vars.$spacing-sm;
  }
}

.imagine-button {
  padding: vars.$spacing-sm vars.$spacing-md;
  border-radius: vars.$border-radius-md;
  cursor: pointer;
  border: 1px solid vars.$color-border;
  background-color: vars.$color-light;
  margin-left: vars.$spacing-sm;
  
  &--primary {
    background-color: vars.$color-primary;
    border-color: vars.$color-primary;
    color: vars.$color-white;
    
    &:hover {
      background-color: vars.$color-primary-dark;
    }
  }
  
  &--secondary {
    &:hover {
      background-color: color.adjust(vars.$color-light, $lightness: -5%);
    }
  }
}

.imagine-empty-state {
  padding: vars.$spacing-xl vars.$spacing-md;
  text-align: center;
  color: vars.$color-text-light;
  font-style: italic;
}
</style>
