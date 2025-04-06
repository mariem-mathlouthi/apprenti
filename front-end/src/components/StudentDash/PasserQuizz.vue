<template>
  <div class="min-h-screen bg-gray-50">
    <NavBarStd />
    <Sidebar class="w-64 fixed" />

    <main class="flex-1 overflow-auto pt-16 pl-64 pr-6 pb-6">
      <div class="max-w-3xl mx-auto">
        <!-- Carte principale du quiz -->
        <div 
          v-if="!quizCompleted" 
          class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl"
        >
          <!-- En-tête du quiz -->
          <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5">
            <div class="flex justify-between items-center text-white">
              <div>
                <h1 class="text-2xl font-bold">QUIZ</h1>
                <div class="text-indigo-100 text-sm mt-1">
                  Question {{ currentQuestionIndex + 1 }}/{{ quizData.length }}
                </div>
              </div>
              <div 
                v-if="currentQuestion.timeLimit"
                class="flex items-center space-x-2 bg-indigo-500/30 rounded-full px-3 py-1"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">{{ formatTime(remainingTime) }}</span>
              </div>
            </div>
          </div>

          <!-- Contenu du quiz -->
          <div class="p-6">
            <!-- Titre du quiz -->
            <div class="flex items-start mb-6">
              <div class="bg-indigo-100 text-indigo-600 rounded-lg p-2 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-1">{{ quizTitle }}</h2>
                <h3 class="text-xl font-medium text-gray-900">{{ currentQuestion.question }}</h3>
              </div>
            </div>

            <!-- Barre de progression du temps -->
            <div 
              v-if="currentQuestion.timeLimit"
              class="mb-6"
            >
              <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div 
                  class="h-full transition-all duration-1000 ease-linear"
                  :class="{
                    'bg-emerald-500': timePercentage > 50,
                    'bg-amber-400': timePercentage <= 50 && timePercentage > 20,
                    'bg-red-500': timePercentage <= 20
                  }"
                  :style="{ width: `${timePercentage}%` }"
                ></div>
              </div>
            </div>

            <!-- Réponses -->
            <div class="space-y-3">
              <div
                v-for="(answer, index) in shuffledAnswers"
                :key="index"
                class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                :class="{
                  'border-emerald-500 bg-emerald-50': showResults && correctAnswers.includes(answer),
                  'border-red-400 bg-red-50': showResults && !correctAnswers.includes(answer) && selectedAnswers.includes(answer),
                  'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50': !showResults,
                  'border-indigo-400 bg-indigo-50': !showResults && selectedAnswers.includes(answer)
                }"
                @click="toggleAnswer(answer)"
              >
                <div class="flex items-center h-5 mr-3">
                  <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-800 font-medium text-sm">
                    {{ String.fromCharCode(65 + index) }}
                  </span>
                </div>
                <div class="flex-1">
                  <p 
                    class="text-gray-800 font-medium"
                    :class="{
                      'text-emerald-700': showResults && correctAnswers.includes(answer),
                      'text-red-700': showResults && !correctAnswers.includes(answer) && selectedAnswers.includes(answer)
                    }"
                  >
                    {{ answer }}
                  </p>
                </div>
                <div 
                  v-if="showResults"
                  class="ml-3"
                >
                  <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-5 w-5"
                    :class="{
                      'text-emerald-500': correctAnswers.includes(answer),
                      'text-red-500': !correctAnswers.includes(answer) && selectedAnswers.includes(answer)
                    }"
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                  >
                    <path 
                      stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      :d="correctAnswers.includes(answer) ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" 
                    />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="px-6 pb-6">
            <div 
              v-if="timeExpired" 
              class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-lg"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-red-700">
                    Temps écoulé !
                  </p>
                </div>
              </div>
            </div>

            <button
              v-if="!quizCompleted && !timeExpired && !showResults"
              @click="checkAnswer"
              class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 disabled:opacity-50 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center"
              :disabled="selectedAnswers.length === 0"
            >
              <span class="font-medium">Valider</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>

            <button
              v-if="showResults && !timeExpired"
              @click="nextQuestion"
              class="w-full px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg"
            >
              {{ currentQuestionIndex < quizData.length - 1 ? 'Question suivante' : 'Voir les résultats' }}
            </button>

            <button
              v-if="timeExpired"
              @click="nextQuestion"
              class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-md hover:shadow-lg"
            >
              Continuer
            </button>
          </div>
        </div>

        <!-- Résultats finaux -->
        <transition name="fade">
          <div 
            v-if="quizCompleted" 
            class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4"
          >
            <transition name="zoom">
              <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-md">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 text-center">
                  <h2 class="text-2xl font-bold text-white">Quiz Terminé !</h2>
                  <p class="text-indigo-100 mt-1">Votre performance</p>
                </div>
                
                <div class="p-8 text-center">
                  <!-- Cercle de progression -->
                  <div class="relative w-48 h-48 mx-auto mb-6">
                    <svg class="w-full h-full" viewBox="0 0 36 36">
                      <path
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none"
                        stroke="#E5E7EB"
                        stroke-width="4"
                      />
                      <path
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none"
                        stroke="#4F46E5"
                        stroke-width="4"
                        stroke-dasharray="100"
                        :stroke-dashoffset="100 - scorePercentage"
                        class="transition-all duration-1000 ease-out"
                      />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                      <span class="text-4xl font-bold text-gray-800">{{ score }}</span>
                      <span class="text-gray-500">points</span>
                    </div>
                  </div>
                  
                  <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                      {{ getResultMessage() }}
                    </h3>
                    <p class="text-gray-600">
                      Vous avez répondu correctement à {{ correctCount }} sur {{ quizData.length }} questions
                    </p>
                  </div>
                  
                  <router-link 
                    :to="{ name: 'ListeQuizz', params: { idCours: quizData[0]?.idCours } }" 
                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-lg hover:shadow-xl text-lg font-medium"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour aux quiz
                  </router-link>
                </div>
                
                <!-- Confettis virtuels -->
                <div class="absolute top-0 left-0 right-0 flex justify-center">
                  <div 
                    v-for="i in 5" 
                    :key="i"
                    class="w-2 h-2 bg-yellow-400 rounded-full absolute opacity-0"
                    :class="`confetti-${i}`"
                    :style="{
                      top: `${Math.random() * 20}%`,
                      left: `${10 + (i * 15)}%`,
                      animation: `confetti 2s ease-out ${i * 0.2}s forwards`
                    }"
                  ></div>
                </div>
              </div>
            </transition>
          </div>
        </transition>
      </div>
    </main>
  </div>
</template>

<script>
import axios from "axios";
import NavBarStd from "./NavBarStd.vue";
import Sidebar from "./Sidebar.vue";

export default {
  name: "PasserQuizz",
  components: { NavBarStd, Sidebar },
  data() {
    return {
      quizData: [],
      currentQuestionIndex: 0,
      selectedAnswers: [],
      score: 0,
      quizCompleted: false,
      quizTitle: "",
      showResults: false,
      remainingTime: 0,
      timer: null,
      timeExpired: false,
      correctCount: 0
    };
  },
  computed: {
    currentQuestion() {
      const question = this.quizData[this.currentQuestionIndex] || {};
      return {
        points: 10,
        timeLimit: 10,
        multiple: false,
        ...question
      };
    },
    shuffledAnswers() {
      if (!this.currentQuestion) return [];
      return [
        ...this.ensureArray(this.currentQuestion.reponseCorrecte),
        ...this.ensureArray(this.currentQuestion.reponsesFausses),
      ].sort(() => Math.random() - 0.5);
    },
    correctAnswers() {
      return this.ensureArray(this.currentQuestion.reponseCorrecte);
    },
    totalPossibleScore() {
      return this.quizData.reduce((total, question) => total + (question.points || 10), 0);
    },
    timePercentage() {
      return (this.remainingTime / this.currentQuestion.timeLimit) * 100;
    },
    scorePercentage() {
      return (this.score / this.totalPossibleScore) * 100;
    }
  },
  methods: {
    async fetchQuiz() {
      try {
        const response = await axios.get(
          `http://localhost:8000/api/quizz/${this.$route.params.idQuizz}`
        );
        this.quizData = Array.isArray(response.data.data) 
          ? response.data.data 
          : [response.data.data];
        this.quizTitle = this.quizData[0]?.titre || "Quiz";
        this.startTimer();
      } catch (error) {
        console.error("Erreur:", error);
      }
    },

    ensureArray(data) {
      if (Array.isArray(data)) return data;
      if (typeof data === "string") {
        try {
          return JSON.parse(data);
        } catch {
          return [data];
        }
      }
      return [data];
    },

    toggleAnswer(answer) {
      if (this.showResults) return;
      
      if (this.currentQuestion.multiple) {
        const index = this.selectedAnswers.indexOf(answer);
        if (index === -1) {
          this.selectedAnswers.push(answer);
        } else {
          this.selectedAnswers.splice(index, 1);
        }
      } else {
        this.selectedAnswers = [answer];
      }
    },

    startTimer() {
      if (this.timer) clearInterval(this.timer);
      
      if (this.currentQuestion.timeLimit) {
        this.remainingTime = this.currentQuestion.timeLimit;
        this.timeExpired = false;
        
        this.timer = setInterval(() => {
          this.remainingTime--;
          
          if (this.remainingTime <= 0) {
            clearInterval(this.timer);
            this.timeExpired = true;
          }
        }, 1000);
      }
    },

    checkAnswer() {
      this.showResults = true;
      const correctAnswers = this.correctAnswers;
      
      let isCorrect;
      if (this.currentQuestion.multiple) {
        const allCorrectSelected = correctAnswers.every(ans => this.selectedAnswers.includes(ans));
        const noIncorrectSelected = this.selectedAnswers.every(ans => correctAnswers.includes(ans));
        isCorrect = allCorrectSelected && noIncorrectSelected;
      } else {
        isCorrect = correctAnswers.includes(this.selectedAnswers[0]);
      }
      
      if (isCorrect) {
        this.score += this.currentQuestion.points;
        this.correctCount++;
      }
    },

    nextQuestion() {
      this.showResults = false;
      this.timeExpired = false;
      this.selectedAnswers = [];
      
      if (this.currentQuestionIndex < this.quizData.length - 1) {
        this.currentQuestionIndex++;
        this.startTimer();
      } else {
        this.quizCompleted = true;
        this.submitScoreToServer();
      }
    },

    getResultMessage() {
      const percentage = this.scorePercentage;
      if (percentage >= 80) return "Excellent travail !";
      if (percentage >= 60) return "Bon résultat !";
      if (percentage >= 40) return "Peut mieux faire";
      return "Continuez à vous exercer";
    },

    formatTime(seconds) {
      const mins = Math.floor(seconds / 60);
      const secs = seconds % 60;
      return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    },

    async submitScoreToServer() {
      try {
        await axios.post(
          `http://localhost:8000/api/quizz/${this.$route.params.idQuizz}/submit`,
          {
            score: this.score,
            reponses: this.selectedAnswers
          }
        );
      } catch (error) {
        console.error("Erreur lors de l'enregistrement du score:", error);
      }
    },
  },
  mounted() {
    this.fetchQuiz();
  },
  beforeUnmount() {
    if (this.timer) clearInterval(this.timer);
  },
};
</script>

<style>
/* Animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.zoom-enter-active {
  animation: zoomIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.zoom-leave-active {
  animation: zoomOut 0.3s ease-in;
}

@keyframes zoomIn {
  from {
    transform: scale(0.9);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes zoomOut {
  from {
    transform: scale(1);
    opacity: 1;
  }
  to {
    transform: scale(0.9);
    opacity: 0;
  }
}

/* Animation des confettis */
@keyframes confetti {
  0% {
    transform: translateY(0) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(100vh) rotate(360deg);
    opacity: 0;
  }
}

/* Style du cercle de progression */
path[stroke="#4F46E5"] {
  transition: stroke-dashoffset 1s ease-out;
}

/* Améliorations globales */
.border-2 {
  border-width: 2px;
}
.rounded-xl {
  border-radius: 12px;
}
.rounded-2xl {
  border-radius: 16px;
}
.shadow-lg {
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}
.shadow-xl {
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}
</style>