<template>
  <div class="editor-canvas">
    <div v-if="blocks.length === 0" class="editor-canvas__empty">
      <p>Your canvas is empty</p>
      <p>Add blocks from the left sidebar to get started</p>
    </div>
    <draggable
      v-else
      v-model="localBlocks"
      group="blocks"
      item-key="id"
      handle=".block-handle"
      @end="emitUpdate"
    >
      <template #item="{ element, index }">
        <div 
          class="editor-block"
          :class="{ 'editor-block--selected': selectedIndex === index }"
          @click="selectBlock(index)"
        >
          <div class="editor-block__toolbar">
            <span class="block-handle dashicons dashicons-move"></span>
            <span class="block-type">{{ getBlockLabel(element.type) }}</span>
            <button 
              class="block-delete dashicons dashicons-trash" 
              @click.stop="deleteBlock(index)"
            ></button>
          </div>
          <div class="editor-block__content">
            <component 
              :is="getBlockComponent(element.type)"
              :block="element"
              @update:block="(updatedBlock) => updateBlock(index, updatedBlock)"
            />
          </div>
        </div>
      </template>
    </draggable>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';
import HeaderBlock from './blocks/HeaderBlock.vue';
import ParagraphBlock from './blocks/ParagraphBlock.vue';

export default {
  components: {
    draggable,
    HeaderBlock,
    ParagraphBlock,
  },
  
  props: {
    blocks: {
      type: Array,
      default: () => []
    }
  },
  
  emits: ['update:blocks', 'select-block'],
  
  setup(props, { emit }) {
    const localBlocks = ref([...props.blocks]);
    const selectedIndex = ref(null);
    
    const blockComponents = {
      header: HeaderBlock,
      paragraph: ParagraphBlock,
    };
    
    const blockLabels = {
      header: 'Header',
      paragraph: 'Paragraph',
    };
    
    const getBlockComponent = (type) => {
      return blockComponents[type] || null;
    };
    
    const getBlockLabel = (type) => {
      return blockLabels[type] || 'Unknown Block';
    };
    
    const emitUpdate = () => {
      emit('update:blocks', localBlocks.value);
    };
    
    const selectBlock = (index) => {
      selectedIndex.value = index;
      emit('select-block', index);
    };
    
    const updateBlock = (index, updatedBlock) => {
      localBlocks.value[index] = updatedBlock;
      emitUpdate();
    };
    
    const deleteBlock = (index) => {
      localBlocks.value.splice(index, 1);
      if (selectedIndex.value === index) {
        selectedIndex.value = null;
      }
      emitUpdate();
    };
    
    return {
      localBlocks,
      selectedIndex,
      getBlockComponent,
      getBlockLabel,
      selectBlock,
      updateBlock,
      deleteBlock,
      emitUpdate
    };
  }
};
</script>

<style lang="scss">
/* Modern Sass usage - no @use "sass:color" since it's in Vite config */
@use "@/styles/_variables.scss" as vars;

.editor-canvas {
  min-height: 400px;
  background-color: vars.$color-white;
  border: 1px solid vars.$color-border;
  border-radius: vars.$border-radius-md;
  padding: vars.$spacing-lg;
  
  &__empty {
    text-align: center;
    padding: 80px 0;
    color: vars.$color-text-light;
    font-style: italic;
  }
}

.editor-block {
  margin-bottom: vars.$spacing-md;
  border: 1px solid vars.$color-border;
  border-radius: vars.$border-radius-md;
  overflow: hidden;
  
  &--selected {
    border-color: vars.$color-primary;
    box-shadow: 0 0 0 1px vars.$color-primary;
  }
  
  &__toolbar {
    display: flex;
    padding: vars.$spacing-xs vars.$spacing-sm;
    background-color: vars.$color-light;
    border-bottom: 1px solid vars.$color-border;
    align-items: center;
    font-size: vars.$font-size-xs;
    
    .block-handle {
      cursor: move;
      margin-right: vars.$spacing-sm;
    }
    
    .block-type {
      flex: 1;
      font-weight: 500;
    }
    
    .block-delete {
      cursor: pointer;
      color: vars.$color-danger;
      background: transparent;
      border: none;
      padding: 0;
    }
  }
  
  &__content {
    padding: vars.$spacing-sm;
  }
}
</style>
