<template>
  <div class="file-upload-container">
    <!-- Hidden file input -->
    <input
      type="file"
      ref="fileInput"
      @change="handleFileChange"
      class="hidden"
      :accept="acceptedFileTypes"
    />

    <!-- Upload button -->
    <button
      v-if="!selectedFile"
      type="button"
      @click="triggerFileInput"
      class="upload-button p-2 rounded-lg bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
      :disabled="disabled"
    >
      <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
      </svg>
    </button>

    <!-- Selected file preview -->
    <div v-if="selectedFile" class="selected-file flex items-center justify-between bg-gray-50 p-2 rounded-lg w-full">
      <!-- File icon and name -->
      <div class="flex items-center space-x-2 overflow-hidden">
        <svg class="w-5 h-5 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span class="text-sm text-gray-600 truncate">{{ selectedFile.name }}</span>
      </div>

      <!-- Remove button -->
      <button 
        @click="removeFile" 
        type="button"
        class="text-red-500 hover:text-red-600 focus:outline-none ml-2 flex-shrink-0"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Upload progress (optional) -->
    <div v-if="uploading" class="upload-progress mt-2">
      <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
        <div 
          class="h-full bg-blue-500 transition-all duration-300" 
          :style="{ width: `${uploadProgress}%` }"
        ></div>
      </div>
      <span class="text-xs text-gray-500 mt-1">{{ uploadProgress }}% uploaded</span>
    </div>

    <!-- Error message -->
    <p v-if="errorMessage" class="text-red-500 text-xs mt-1">{{ errorMessage }}</p>
  </div>
</template>

<script>
export default {
  name: 'ChatFileUpload',
  props: {
    disabled: {
      type: Boolean,
      default: false
    },
    maxSizeMB: {
      type: Number,
      default: 2 // Default to 2MB as per backend validation
    }
  },
  data() {
    return {
      selectedFile: null,
      errorMessage: '',
      uploading: false,
      uploadProgress: 0,
      // Match the backend validation requirements
      allowedFileTypes: ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']
    };
  },
  computed: {
    acceptedFileTypes() {
      return this.allowedFileTypes.join(',');
    }
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    
    handleFileChange(event) {
      const file = event.target.files[0];
      if (!file) return;
      
      this.errorMessage = '';
      
      // Validate file type
      if (!this.allowedFileTypes.includes(file.type)) {
        this.errorMessage = 'Type de fichier non autorisé. Formats acceptés : JPG, PNG, PDF';
        this.$refs.fileInput.value = '';
        return;
      }
      
      // Validate file size
      const maxSizeBytes = this.maxSizeMB * 1024 * 1024;
      if (file.size > maxSizeBytes) {
        this.errorMessage = `Le fichier est trop volumineux. Taille maximale : ${this.maxSizeMB}MB`;
        this.$refs.fileInput.value = '';
        return;
      }
      
      // Set the selected file
      this.selectedFile = file;
      
      // Emit the file selected event
      this.$emit('file-selected', file);
    },
    
    removeFile() {
      this.selectedFile = null;
      this.errorMessage = '';
      this.uploading = false;
      this.uploadProgress = 0;
      this.$refs.fileInput.value = '';
      
      // Emit the file removed event
      this.$emit('file-removed');
    },
    
    // Method to simulate or track upload progress
    // This would be used when actually uploading the file
    startUpload() {
      this.uploading = true;
      this.uploadProgress = 0;
      
      // In a real implementation, you would track the upload progress
      // For now, we'll just simulate it
      const interval = setInterval(() => {
        this.uploadProgress += 10;
        if (this.uploadProgress >= 100) {
          clearInterval(interval);
          this.uploading = false;
        }
      }, 300);
    },
    
    // Reset the component
    reset() {
      this.removeFile();
    }
  }
};
</script>

<style scoped>
.hidden {
  display: none;
}

.file-upload-container {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.selected-file {
  flex: 1;
  min-width: 0; /* Allows the container to shrink below its content size */
}
</style>