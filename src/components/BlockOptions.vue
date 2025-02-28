<template>
  <div class="block-options">
    <h3>{{ getBlockTitle(localBlock.type) }} Options</h3>
    
    <!-- Common options for all blocks -->
    <div class="option-group">
      <label>Block ID:</label>
      <span class="block-id">{{ localBlock.id }}</span>
    </div>
    
    <!-- Type-specific options -->
    <component
      :is="getOptionsComponent(localBlock.type)"
      :block="localBlock"
      @update:block="updateAndEmit"
      v-if="getOptionsComponent(localBlock.type)"
    ></component>
    
    <!-- Content edit -->
    <div class="option-group">
      <label>Content:</label>
      <textarea 
        v-model="localBlock.content"
        @input="updateAndEmit"
        rows="5"
      ></textarea>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import HeaderOptions from './options/HeaderOptions.vue';
import ParagraphOptions from './options/ParagraphOptions.vue';

export default {
  components: {
    HeaderOptions,
    ParagraphOptions
  },
  
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  
  emits: ['update:block'],
  
  setup(props, { emit }) {
    const localBlock = ref({ ...props.block });
    
    const blockTitles = {
      header: 'Header',
      paragraph: 'Paragraph'
    };
    
    const optionsComponents = {
      header: HeaderOptions,
      paragraph: ParagraphOptions
    };
    
    const getBlockTitle = (type) => {
      return blockTitles[type] || 'Block';
    };
    
    const getOptionsComponent = (type) => {
      return optionsComponents[type] || null;
    };
    
    const updateAndEmit = () => {
      emit('update:block', { ...localBlock.value });
    };
    
    // Watch for changes in the prop to update the local copy
    watch(() => props.block, (newVal) => {
      localBlock.value = { ...newVal };
    });
    
    return {
      localBlock,
      getBlockTitle,
      getOptionsComponent,
      updateAndEmit
    };
  }
}
</script>

<style lang="scss">
/* Modern Sass usage - no @use "sass:color" since it's in Vite config */
@use "@/styles/_variables.scss" as vars;

.block-options {
  h3 {
    margin-top: 0;
    margin-bottom: vars.$spacing-md;
    padding-bottom: vars.$spacing-sm;
    border-bottom: 1px solid vars.$color-border;
  }
  
  .option-group {
    margin-bottom: vars.$spacing-md;
    
    label {
      display: block;
      margin-bottom: vars.$spacing-xs;
      font-weight: 500;
    }
    
    .block-id {
      display: block;
      padding: vars.$spacing-xs;
      background-color: vars.$color-light;
      border: 1px solid vars.$color-border;
      border-radius: vars.$border-radius-sm;
      font-family: monospace;
      color: vars.$color-text-light;
    }
    
    input, select, textarea {
      width: 100%;
      padding: vars.$spacing-sm;
      border: 1px solid vars.$color-border;
      border-radius: vars.$border-radius-md;
      
      &:focus {
        border-color: vars.$color-primary;
        box-shadow: 0 0 0 1px vars.$color-primary;
        outline: none;
      }
    }
  }
}
</style>
