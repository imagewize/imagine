<template>
  <div class="header-options">
    <div class="option-group">
      <label>Header Level</label>
      <select 
        v-model="localBlock.settings.level" 
        @change="updateAndEmit"
      >
        <option value="h1">Heading 1</option>
        <option value="h2">Heading 2</option>
        <option value="h3">Heading 3</option>
        <option value="h4">Heading 4</option>
        <option value="h5">Heading 5</option>
        <option value="h6">Heading 6</option>
      </select>
    </div>
    
    <div class="option-group">
      <label>Text Alignment</label>
      <div class="alignment-buttons">
        <button 
          type="button" 
          class="alignment-button" 
          :class="{'active': localBlock.settings.alignment === 'left'}"
          @click="setAlignment('left')"
        >
          <span class="dashicons dashicons-align-left"></span>
        </button>
        <button 
          type="button" 
          class="alignment-button"
          :class="{'active': localBlock.settings.alignment === 'center'}"
          @click="setAlignment('center')"
        >
          <span class="dashicons dashicons-align-center"></span>
        </button>
        <button 
          type="button" 
          class="alignment-button"
          :class="{'active': localBlock.settings.alignment === 'right'}"
          @click="setAlignment('right')"
        >
          <span class="dashicons dashicons-align-right"></span>
        </button>
      </div>
    </div>
    
    <div class="option-group">
      <label>Color</label>
      <div class="color-picker-container">
        <input 
          type="color" 
          v-model="localBlock.settings.color"
          @change="updateAndEmit"
        />
        <input 
          type="text" 
          v-model="localBlock.settings.color"
          @change="updateAndEmit"
          placeholder="#000000"
        />
      </div>
    </div>
    
    <div class="option-group">
      <label>Font Size (px)</label>
      <div class="font-size-control">
        <input 
          type="range" 
          v-model.number="localBlock.settings.fontSize" 
          min="12" 
          max="72" 
          @change="updateAndEmit"
        />
        <input 
          type="number" 
          v-model.number="localBlock.settings.fontSize" 
          min="12" 
          max="72" 
          @change="updateAndEmit"
        />
      </div>
    </div>
    
    <div class="option-group">
      <label>Font Weight</label>
      <select 
        v-model="localBlock.settings.fontWeight" 
        @change="updateAndEmit"
      >
        <option value="normal">Normal</option>
        <option value="bold">Bold</option>
        <option value="300">Light (300)</option>
        <option value="600">Semi-Bold (600)</option>
        <option value="800">Extra-Bold (800)</option>
      </select>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';

export default {
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  
  emits: ['update:block'],
  
  setup(props, { emit }) {
    // Create a local copy of the block to modify
    const localBlock = ref({
      ...props.block,
      settings: {
        level: 'h2',
        alignment: 'left',
        color: '',
        fontSize: '',
        fontWeight: 'normal',
        ...props.block.settings
      }
    });
    
    // Watch for external changes
    watch(() => props.block, (newVal) => {
      localBlock.value = {
        ...newVal,
        settings: {
          level: 'h2',
          alignment: 'left',
          color: '',
          fontSize: '',
          fontWeight: 'normal',
          ...newVal.settings
        }
      };
    });
    
    const updateAndEmit = () => {
      emit('update:block', { ...localBlock.value });
    };
    
    const setAlignment = (alignment) => {
      localBlock.value.settings.alignment = alignment;
      updateAndEmit();
    };
    
    return {
      localBlock,
      updateAndEmit,
      setAlignment
    };
  }
};
</script>

<style lang="scss">
/* Modern Sass usage - no @use "sass:color" since it's in Vite config */
@use "@/styles/_variables.scss" as vars;

.header-options {
  .option-group {
    margin-bottom: vars.$spacing-md;
  }
  
  .alignment-buttons {
    display: flex;
    gap: vars.$spacing-xs;
    margin-top: vars.$spacing-xs;
    
    .alignment-button {
      flex: 1;
      background: vars.$color-light;
      border: 1px solid vars.$color-border;
      padding: vars.$spacing-xs;
      cursor: pointer;
      
      &:hover {
        background: #eee;
      }
      
      &.active {
        background: vars.$color-primary;
        color: vars.$color-white;
        border-color: vars.$color-primary;
      }
      
      .dashicons {
        display: block;
        margin: 0 auto;
      }
    }
  }
  
  .color-picker-container {
    display: flex;
    align-items: center;
    margin-top: vars.$spacing-xs;
    
    input[type="color"] {
      min-width: 40px;
      height: 35px;
      padding: 0;
      margin-right: vars.$spacing-sm;
    }
    
    input[type="text"] {
      flex: 1;
    }
  }
  
  .font-size-control {
    display: flex;
    gap: vars.$spacing-sm;
    align-items: center;
    margin-top: vars.$spacing-xs;
    
    input[type="range"] {
      flex: 1;
    }
    
    input[type="number"] {
      width: 60px;
    }
  }
}
</style>
