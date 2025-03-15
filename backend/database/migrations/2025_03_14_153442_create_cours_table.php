<template>
  <div>
    <h2>Create New Course</h2>
    <form @submit.prevent="submitForm">
      <div>
        <label for="titre">Title</label>
        <input type="text" id="titre" v-model="cours.titre" required />
      </div>
      
      <div>
        <label for="description">Description</label>
        <textarea id="description" v-model="cours.description" required></textarea>
      </div>
      
      <div>
        <label for="category_id">Category</label>
        <select v-model="cours.category_id" required>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
        </select>
      </div>
      
      <div>
        <label for="prix">Price</label>
        <input type="number" id="prix" v-model="cours.prix" required />
      </div>
      
      <div>
        <label for="idTuteur">Instructor</label>
        <select v-model="cours.idTuteur" required>
          <option v-for="tuteur in tutors" :key="tuteur.id" :value="tuteur.id">{{ tuteur.name }}</option>
        </select>
      </div>
      
      <div>
        <label for="idApprenant">Student</label>
        <select v-model="cours.idApprenant">
          <option v-for="student in students" :key="student.id" :value="student.id">{{ student.name }}</option>
        </select>
      </div>
      
      <div>
        <label for="duration">Duration (in minutes)</label>
        <input type="number" id="duration" v-model="cours.duration" required />
      </div>
      
      <div>
        <label for="file">Upload File</label>
        <input type="file" @change="handleFileUpload" />
      </div>
      
      <div>
        <button type="submit">Submit</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      cours: {
        titre: '',
        description: '',
        category_id: '',
        prix: '',
        idTuteur: '',
        idApprenant: '',
        duration: '',
        file: null
      },
      categories: [],
      tutors: [],
      students: []
    };
  },
  created() {
    this.getCategories();
    this.getTutors();
    this.getStudents();
  },
  methods: {
    getCategories() {
      // Fetch categories from the backend
      axios.get('/api/categories').then(response => {
        this.categories = response.data;
      });
    },
    getTutors() {
      // Fetch tutors from the backend
      axios.get('/api/tuteurs').then(response => {
        this.tutors = response.data;
      });
    },
    getStudents() {
      // Fetch students from the backend
      axios.get('/api/etudiants').then(response => {
        this.students = response.data;
      });
    },
    handleFileUpload(event) {
      this.cours.file = event.target.files[0];
    },
    submitForm() {
      // Handle form submission
      const formData = new FormData();
      formData.append('titre', this.cours.titre);
      formData.append('description', this.cours.description);
      formData.append('category_id', this.cours.category_id);
      formData.append('prix', this.cours.prix);
      formData.append('idTuteur', this.cours.idTuteur);
      formData.append('idApprenant', this.cours.idApprenant);
      formData.append('duration', this.cours.duration);
      if (this.cours.file) {
        formData.append('file', this.cours.file);
      }

      // Post form data to backend for creating the course
      axios.post('/api/cours', formData).then(response => {
        this.$router.push('/cours'); // Navigate to course listing or another page after submission
      }).catch(error => {
        console.error(error);
      });
    }
  }
};
</script>

<style scoped>
/* Add styling for the form */
</style>
