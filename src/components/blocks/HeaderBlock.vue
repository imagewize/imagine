<template>
  <div class="block-header">
    <component 
      :is="localBlock.settings.level || 'h2'" 
      class="header-content"
      :style="headerStyles"
      v-text="localBlock.content"
    ></component>
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
    
    return {
      localBlock,
      headerStyles
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
</style>
