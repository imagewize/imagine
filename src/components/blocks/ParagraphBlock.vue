<template>
  <div class="block-paragraph">
    <p 
      class="paragraph-content" 
      :style="paragraphStyles"
      v-text="localBlock.content"
    ></p>
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
  
  setup(props) {
    const localBlock = ref({ ...props.block });
    
    // Initialize default settings if not present
    if (!localBlock.value.settings) {
      localBlock.value.settings = {
        alignment: 'left',
        color: '',
        fontSize: '',
        lineHeight: '1.5'
      };
    }
    
    const paragraphStyles = computed(() => {
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
      
      if (localBlock.value.settings.lineHeight) {
        styles.lineHeight = localBlock.value.settings.lineHeight;
      }
      
      return styles;
    });
    
    // Watch for changes in props
    watch(() => props.block, (newVal) => {
      localBlock.value = { ...newVal };
    });
    
    return {
      localBlock,
      paragraphStyles
    };
  }
}
</script>

<style lang="scss">
/* Modern Sass usage - no @use "sass:color" since it's in Vite config */
@use "@/styles/_variables.scss" as vars;

.block-paragraph {
  padding: vars.$spacing-sm 0;
  
  .paragraph-content {
    margin: 0;
    padding: 0;
    outline: none;
    
    &:hover {
      background-color: color.adjust(#000000, $alpha: -0.97);
    }
  }
}
</style>
