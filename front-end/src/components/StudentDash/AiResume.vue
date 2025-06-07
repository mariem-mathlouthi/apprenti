<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50/20">
    <!-- Fixed Navbar -->
    <NavBarStd
      class="fixed top-0 left-0 w-full z-50 shadow-sm bg-white/90 backdrop-blur-md"
    />

    <!-- Main Content with Sidebar -->
    <div class="flex">
      <!-- Fixed Sidebar -->
      <Sidebar class="fixed left-0 top-16" />

      <!-- Main Content Area -->
      <main class="flex-1 ml-64 p-8 pt-24">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-8">
          <!-- Header -->
          <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Résumé de Cours IA</h1>
            <p class="mt-2 text-gray-600">
              Transformez vos supports de cours en résumés concis
            </p>
          </div>

          <!-- Upload Card -->
          <div
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
          >
            <!-- Upload Zone -->
            <div class="p-6">
              <div
                class="relative group"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleFileDrop"
              >
                <div
                  class="border-2 border-dashed rounded-xl transition-all duration-200"
                  :class="{
                    'border-indigo-400 bg-indigo-50': isDragging,
                    'border-gray-300 hover:border-indigo-400': !isDragging,
                  }"
                >
                  <div class="p-8 text-center">
                    <!-- Upload Icon -->
                    <div
                      class="inline-flex p-3 rounded-full bg-indigo-100 mb-4"
                    >
                      <svg
                        class="h-8 w-8 text-indigo-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                        />
                      </svg>
                    </div>
                    <!-- Upload Text -->
                    <div>
                      <label class="relative cursor-pointer">
                        <span class="text-gray-700 text-lg font-medium">
                          {{
                            selectedFile
                              ? selectedFile.name
                              : "Déposez votre PDF ici"
                          }}
                        </span>
                        <span
                          class="text-indigo-600 hover:text-indigo-500 ml-1"
                        >
                          ou parcourir
                        </span>
                        <input
                          type="file"
                          class="hidden"
                          accept=".pdf"
                          @change="handleFileSelect"
                        />
                      </label>
                      <p class="text-sm text-gray-500 mt-2">
                        Fichiers PDF uniquement
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- File Preview -->
            <div
              v-if="selectedFile"
              class="border-t border-gray-200 bg-gray-50 p-4"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <svg
                      class="h-8 w-8 text-red-500"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"
                      />
                      <path
                        d="M3 8a2 2 0 012-2v10h2a2 2 0 002 2H5a2 2 0 01-2-2V8z"
                      />
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ selectedFile.name }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ formatFileSize(selectedFile.size) }}
                    </p>
                  </div>
                </div>
                <button
                  @click="clearFile"
                  class="text-sm font-medium text-red-600 hover:text-red-500"
                >
                  Supprimer
                </button>
              </div>
            </div>
          </div>

          <!-- Action Button -->
          <div class="mt-6 flex justify-center">
            <button
              @click="generateSummary"
              :disabled="!selectedFile || isLoading"
              class="relative inline-flex items-center px-3 py-3 border border-transparent text-base font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 disabled:hover:scale-100"
              :class="{
                'animate-pulse': isLoading,
                'shadow-2xl': !isLoading
              }"
            >
              <!-- Loading overlay -->
              <div 
                v-if="isLoading" 
                class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center"
              >
                <div class="flex items-center space-x-3">
                  <div class="relative">
                    <div class="w-5 h-5 border-2 border-white/30 rounded-full animate-spin border-t-white"></div>
                  </div>
                  <span class="text-white font-medium">{{ loadingText }}</span>
                </div>
              </div>
              
              <!-- Default button content -->
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <div :class="{ 'opacity-0': isLoading }">
                Générer le résumé
              </div>
            </button>
          </div>

          <!-- Loading Progress Bar -->
          <div v-if="isLoading" class="mt-4">
            <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
              <div 
                class="bg-gradient-to-r from-indigo-500 to-purple-500 h-full rounded-full transition-all duration-1000 ease-out"
                :style="{ width: loadingProgress + '%' }"
              ></div>
            </div>
            <p class="text-center text-sm text-gray-600 mt-2">{{ loadingText }}</p>
          </div>

          <!-- Summary Result -->
          <div
            v-if="summary"
            class="mt-8 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden transform transition-all duration-500 ease-out"
            :class="{ 'animate-fade-in': summary }"
          >
            <!-- Summary Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-xl font-bold text-gray-900">Résumé Généré</h2>
                    <p class="text-sm text-gray-600">Analyse complète de votre document</p>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="copyToClipboard"
                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                    title="Copier le résumé"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                  </button>
                  <button
                    @click="downloadSummary"
                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                    title="Télécharger le résumé"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Summary Content -->
            <div class="p-6">
              <div class="prose prose-lg prose-indigo max-w-none">
                <div class="bg-gradient-to-br from-gray-50 to-indigo-50/30 rounded-xl p-6 text-gray-800 leading-relaxed border border-gray-100">
                  <div class="whitespace-pre-line">{{ summary }}</div>
                </div>
              </div>
            </div>

            <!-- Summary Stats -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
              <div class="flex items-center justify-between text-sm text-gray-600">
                <span>{{ wordCount }} mots • {{ characterCount }} caractères</span>
                <span>Généré le {{ formatDate(new Date()) }}</span>
              </div>
            </div>
          </div>

          <!-- Success Message -->
          <div
            v-if="showSuccessMessage"
            class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center space-x-3 animate-fade-in"
          >
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <p class="text-green-800">{{ successMessage }}</p>
          </div>

          <!-- Error Message -->
          <div 
            v-if="error" 
            class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center space-x-3 animate-fade-in"
          >
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-red-800 font-medium">Erreur</p>
              <p class="text-red-700 text-sm">{{ error }}</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import NavBarStd from "./NavBarStd.vue";
import Sidebar from "./Sidebar.vue";
import { GoogleGenAI } from "@google/genai";
import { Buffer } from 'buffer';

const selectedFile = ref(null);
const isDragging = ref(false);
const isLoading = ref(false);
const summary = ref("");
const error = ref(null);
const loadingProgress = ref(0);
const loadingText = ref("Initialisation...");
const showSuccessMessage = ref(false);
const successMessage = ref("");

// Computed properties for summary stats
const wordCount = computed(() => {
  if (!summary.value) return 0;
  return summary.value.trim().split(/\s+/).length;
});

const characterCount = computed(() => {
  return summary.value ? summary.value.length : 0;
});

// Loading messages
const loadingMessages = [
  "Initialisation...",
  "Lecture du document PDF...",
  "Analyse du contenu...",
  "Extraction des informations clés...",
  "Génération du résumé...",
  "Finalisation..."
];

// Function to convert File to FileData for Gemini
const fileToGenerativeAIFile = async (file) => {
  const data = await file.arrayBuffer();
  return {
    data,
    mimeType: file.type,
  };
};

const generateSummary = async () => {
  if (!selectedFile.value) {
    error.value = "Veuillez d'abord sélectionner un fichier PDF";
    return;
  }

  try {
    isLoading.value = true;
    error.value = "";
    summary.value = "";
    loadingProgress.value = 0;
    
    // Simulate loading progress
    const progressInterval = setInterval(() => {
      if (loadingProgress.value < 90) {
        loadingProgress.value += Math.random() * 15;
        const messageIndex = Math.min(
          Math.floor((loadingProgress.value / 90) * loadingMessages.length),
          loadingMessages.length - 1
        );
        loadingText.value = loadingMessages[messageIndex];
      }
    }, 800);

    const ai = new GoogleGenAI({ apiKey: "AIzaSyDNsWBwF7GvCg6b2yIgfDOUebL7WG-2aEc" });
    const pdfResp = await selectedFile.value.arrayBuffer();

    const contents = [
      { 
        text: `Analysez ce document PDF et fournissez un résumé détaillé en français. 
               Organisez votre réponse de manière claire et structurée avec :
               - Un titre principal
               - Les points clés organisés par sections
               - Les concepts importants mis en évidence
               - Une conclusion synthétique
               
               IMPORTANT: Utilisez un formatage simple sans symboles markdown (pas de #, ##, ###, **, etc.). 
               Utilisez uniquement du texte brut avec des espaces et des tirets pour structurer le contenu.
               Organisez le contenu avec des paragraphes bien espacés et des listes à puces simples avec des tirets (-).` 
      },
      {
        inlineData: {
          mimeType: 'application/pdf',
          data: Buffer.from(pdfResp).toString("base64")
        }
      }
    ];

    const response = await ai.models.generateContent({
      model: "gemini-2.0-flash",
      contents: contents
    });

    clearInterval(progressInterval);
    loadingProgress.value = 100;
    loadingText.value = "Résumé généré avec succès !";

    setTimeout(() => {
      summary.value = response?.text || "Aucun résumé généré.";
      showSuccessMessage.value = true;
      successMessage.value = "Résumé généré avec succès ! Vous pouvez maintenant le consulter ci-dessous.";
      
      // Hide success message after 5 seconds
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 5000);
    }, 500);

  } catch (err) {
    console.error("Error details:", err);
    error.value = "Une erreur est survenue lors de la génération du résumé. Veuillez vérifier que le fichier PDF n'est pas trop volumineux ou protégé par mot de passe.";
  } finally {
    setTimeout(() => {
      isLoading.value = false;
      loadingProgress.value = 0;
    }, 1000);
  }
};

const handleFileSelect = (event) => {
  const file = event.target.files[0];
  if (file && file.type === "application/pdf") {
    selectedFile.value = file;
    summary.value = "";
    error.value = null;
  } else {
    error.value = "Veuillez sélectionner un fichier PDF valide";
  }
};

const handleFileDrop = (event) => {
  isDragging.value = false;
  const file = event.dataTransfer.files[0];
  if (file && file.type === "application/pdf") {
    selectedFile.value = file;
    summary.value = "";
    error.value = null;
  } else {
    error.value = "Veuillez déposer un fichier PDF valide";
  }
};

const clearFile = () => {
  selectedFile.value = null;
  summary.value = "";
  error.value = null;
  showSuccessMessage.value = false;
};

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(summary.value);
    successMessage.value = "Résumé copié dans le presse-papiers !";
    showSuccessMessage.value = true;
    setTimeout(() => {
      showSuccessMessage.value = false;
    }, 3000);
  } catch (err) {
    error.value = "Impossible de copier le résumé";
  }
};

const downloadSummary = () => {
  const blob = new Blob([summary.value], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `resume-${selectedFile.value?.name.replace('.pdf', '')}-${Date.now()}.txt`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

const formatDate = (date) => {
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}

.prose {
  line-height: 1.7;
}

.prose div {
  font-size: 1rem;
}
</style>