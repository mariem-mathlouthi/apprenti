<template>
  <div class="min-h-screen bg-gray-50">
    <NavBarStd />
    
    <div class="flex pt-16">
      <!-- Sidebar -->
      <Sidebar  />

      <!-- Contenu principal -->
      <main class="flex-1 ml-64 p-8">
        <div class="max-w-2xl mx-auto">
          <!-- En-tête -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
              <h1 class="text-2xl font-bold text-gray-800">{{ quizTitle }}</h1>
              <span class="text-indigo-600 font-semibold text-lg">
                Score: {{ currentScore }}
              </span>
            </div>
            
            <div class="flex justify-between items-center text-sm text-gray-500">
              <span>Question {{ currentQuestionIndex + 1 }}/{{ questions.length }}</span>
              <span>{{ timer }}s restantes</span>
            </div>
          </div>

          <!-- Question actuelle -->
          <div v-if="!quizFinished" class="bg-white rounded-lg shadow-md p-6 transition-all duration-300">
            <h2 class="text-xl font-semibold mb-6 text-gray-800">{{ currentQuestion.question }}</h2>
            
            <!-- Réponses -->
            <div 
              v-for="(answer, index) in currentQuestion.answers" 
              :key="index"
              @click="!selectedAnswer && checkAnswer(answer)"
              :class="[
                'p-4 mb-3 rounded-lg cursor-pointer border transition-all duration-300',
                getAnswerClasses(answer)
              ]"
            >
              <div class="flex items-center">
                <span class="mr-3 font-semibold">{{ String.fromCharCode(65 + index) }}.</span>
                <span class="flex-1">{{ answer.text }}</span>
                <span v-if="selectedAnswer && answer.isCorrect" class="ml-2 text-green-500">✓</span>
              </div>
            </div>
          </div>

          <!-- Résultats finaux -->
          <div v-else class="bg-white rounded-lg shadow-md p-6 text-center">
            <h2 class="text-2xl font-bold text-green-600 mb-4">Quiz Terminé !</h2>
            <div class="mb-6">
              <p class="text-xl">
                Score Final: 
                <span class="font-bold text-indigo-600">{{ currentScore }}</span> /
                <span class="text-gray-600">{{ totalPossibleScore }}</span>
              </p>
              <p class="text-gray-500 mt-2">Taux de réussite: {{ successRate }}%</p>
            </div>
            <button 
              @click="$router.go(-1)"
              class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Retour aux quizz
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import NavBarStd from './NavBarStd.vue'
import Sidebar from './Sidebar.vue'

export default {
  components: { NavBarStd, Sidebar },
  props: ['idCours', 'titreQuizz'],
  
  data() {
    return {
      loading: true,
      questions: [],
      currentQuestionIndex: 0,
      currentScore: 0,
      selectedAnswer: null,
      quizFinished: false,
      totalPossibleScore: 0,
      timer: 15,
      timerInterval: null
    }
  },

  computed: {
    currentQuestion() {
      return this.questions[this.currentQuestionIndex] || {}
    },
    quizTitle() {
      return this.titreQuizz
    },
    successRate() {
      return Math.round((this.currentScore / this.totalPossibleScore) * 100) || 0
    }
  },

  async created() {
    await this.fetchQuestions()
    this.totalPossibleScore = this.questions.reduce((sum, q) => sum + q.score, 0)
    this.startTimer()
  },

  methods: {
    cleanAnswerText(text) {
      const cleaned = String(text || '')
        .replace(/[[\]"']+/g, '')
        .trim()
      return cleaned || 'Réponse manquante'
    },

    async fetchQuestions() {
      try {
        const response = await axios.get(`http://localhost:8000/api/quizz/${this.idCours}/${this.titreQuizz}`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        )
        this.questions = response.data.map(question => ({
          ...question,
          question: this.cleanAnswerText(question.question),
          answers: this.shuffleAnswers(
            question.answers.map(a => ({
              text: this.cleanAnswerText(a.text),
              isCorrect: a.isCorrect
            }))
          ).filter(a => a.text)
        })).filter(q => q.answers.length > 0)
        this.loading = false
      } catch (error) {
        console.error("Erreur de chargement des questions:", error)
      }
    },

    shuffleAnswers(answers) {
      for (let i = answers.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [answers[i], answers[j]] = [answers[j], answers[i]]
      }
      return answers
    },

    startTimer() {
      this.timerInterval = setInterval(() => {
        if (this.timer > 0) this.timer--
        else this.handleTimeout()
      }, 1000)
    },

    getAnswerClasses(answer) {
      if (!this.selectedAnswer) {
        return 'hover:bg-gray-50 border-gray-200'
      }
      
      const isSelected = this.selectedAnswer === answer
      const isCorrect = answer.isCorrect

      return {
        'border-2': true,
        'bg-green-100 border-green-500': isCorrect,
        'bg-red-100 border-red-500': !isCorrect && isSelected,
        'bg-gray-50 border-gray-300': !isCorrect && !isSelected
      }
    },

    checkAnswer(answer) {
      this.selectedAnswer = answer

      if (answer.isCorrect) {
        this.currentScore += this.currentQuestion.score
      }

      clearInterval(this.timerInterval)
      setTimeout(this.nextQuestion, 1500)
    },

    handleTimeout() {
      this.selectedAnswer = { isCorrect: false }
      setTimeout(this.nextQuestion, 1000)
    },

    nextQuestion() {
      if (this.currentQuestionIndex < this.questions.length - 1) {
        this.currentQuestionIndex++
        this.resetQuestion()
      } else {
        this.quizFinished = true
      }
    },

    resetQuestion() {
      this.selectedAnswer = null
      this.timer = 15
      this.startTimer()
    }
  },

  beforeUnmount() {
    clearInterval(this.timerInterval)
  }
}
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>