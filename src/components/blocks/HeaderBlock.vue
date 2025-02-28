<template>
  <div class="block-header">
    <component 
      :is="localBlock.settings.level || 'h2'" 
      class="header-content"
      :style="headerStyles"
      v-model="localBlock.content"
    ></component>
  </div>
  <div class="header-block">
    <label for="header-content">Content:</label>
    <input 
      type="text" 
      id="header-content" 
      :value="block.content"
      @input="updateContent"
    />
    
    <label for="header-level">Level:</label>
    <select id="header-level" :value="block.settings.level" @change="updateLevel">
      <option value="h1">H1</option>
      <option value="h2">H2</option>
      <option value="h3">H3</option>
      <option value="h4">H4</option>
      <option value="h5">H5</option>
      <option value="h6">H6</option>
    </select>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
  props: {
    block: {
      type: Object,
      required: true
    }
  },
  
  emits: ['update:block'],
  
  setup(props, { emit }) {
    const localBlock = ref({ ...props.block });
    
    // Initialize default settings if not present
    if (!localBlock.value.settings) {
      localBlock.value.settings = {
        level: 'h2',
        alignment: 'left',
        color: '',
        fontSize: '',
        fontWeight: 'normal'
      };
    }
    
    const headerStyles = computed(() => {
      const styles = {};
      
      if (localBlock.value.settings.alignment) {
        styles.textAlign = localBlock.value.settings.alignment;
      }
      
      if (localBlock.value.settings.color) {
        styles.color = localBlock.value.settings.color;
      }
      
      if (localBlock.value.settings.fontSize) {
        styles.fontSize = `${localBlock.value.settings.fontSize}px`;
      }
      
      if (localBlock.value.settings.fontWeight) {
        styles.fontWeight = localBlock.value.settings.fontWeight;
      }
      
      return styles;
    });
    
    // Watch for changes in props
    watch(() => props.block, (newVal) => {
      localBlock.value = { ...newVal };
    });

    const updateContent = (event) => {
      emit('update:block', {
        ...props.block,
        content: event.target.value
      });
    };

    const updateLevel = (event) => {
       emit('update:block', {
        ...props.block,
        settings: {
          ...props.block.settings,
          level: event.target.value
        }
      });
    };

    return {
      localBlock,
      headerStyles,
      updateContent,
      updateLevel
    };
  }
}
</script>

<style lang="scss">
/* No need to import color module as it's in additionalData in Vite config */
@use "@/styles/_variables.scss" as vars;

.block-header {
  padding: vars.$spacing-sm 0;
  
  .header-content {
    margin: 0;
    padding: 0;
    outline: none;
    
    &:hover {
      background-color: color.adjust(#000000, $alpha: -0.97);
    }
  }
}

.header-block {
  display: flex;
  flex-direction: column;
}

.header-block label {
  margin-bottom: 5px;
}

.header-block input,
.header-block select {
  margin-bottom: 10px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
