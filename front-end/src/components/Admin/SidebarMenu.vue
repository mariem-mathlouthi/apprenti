<template>
	<aside id="sidebar">
	  <section id="sidebar">
		<a href="#" class="brand">
		  <i class='bx bxs-smile'></i>
		  <span class="text">Admin</span>
		</a>
		<ul class="side-menu top">
		  <li :class="{ active: $route.path === '/Admin' }">
			<a href="#">
			  <i class='bx bxs-dashboard'></i>
			  <router-link to="/Admin"><span class="text">Tableau de bord</span></router-link>
			</a>
		  </li>
		  <li :class="{ active: $route.path === '/TuteursListAdmin' }">
			<a href="#">
			  <i class='bx bxs-user-account'></i>
			  <router-link to="/TuteursListAdmin"><span class="text">Les Tuteurs</span></router-link>
			</a>
		  </li>
		  <li :class="{ active: $route.path === '/CategoryList' }">
			<a href="#">
			  <i class='bx bxs-category'></i>
			  <router-link to="/CategoryList"><span class="text">Catégories</span></router-link>
			</a>
		  </li>
		  <li :class="{ active: $route.path === '/OffresListAdmin' }">
			<a href="#">
			  <i class='bx bxs-offer'></i>
			  <router-link to="/OffresListAdmin"><span class="text">Les offres</span></router-link>
			</a>
		  </li>
		  <li :class="{ active: $route.path === '/UsersList' }">
			<a href="#">
			  <i class='bx bxs-building'></i>
			  <router-link to="/UsersList"><span class="text">Les entreprises</span></router-link>
			</a>
		  </li>
		  <li :class="{ active: $route.path === '/UsersListEtudiants' }">
			<a href="#">
			  <i class='bx bxs-graduation'></i>
			  <router-link to="/UsersListEtudiants"><span class="text">Les étudiants</span></router-link>
			</a>
		  </li>
		  <!-- Nouvel élément Feedbacks -->
		  <li :class="{ active: $route.path === '/AdminFeedbacks' }">
			<a href="#">
			  <i class='bx bxs-message-alt-detail'></i>
			  <router-link to="/AdminFeedbacks"><span class="text">Feedbacks</span></router-link>
			</a>
		  </li>
		</ul>
		<ul class="side-menu">
		  <li>
			<a href="#" class="logout" @click.prevent="logout">
			  <i class='bx bxs-log-out-circle'></i>
			  <span class="text">Déconnexion</span>
			</a>
		  </li>
		</ul>
	  </section>
	</aside>
  </template>
  
  <script>
  export default {
	methods: {
	  logout() {
		// Votre logique de déconnexion
		this.$router.push('/signin');
	  }
	}
  }
  </script>
  
  <style scoped>
  /* Styles pour l'état actif */
  .side-menu li.active {
	background-color: rgba(255, 255, 255, 0.1);
	border-left: 4px solid #3a7bd5;
  }
  
  .side-menu li.active a,
  .side-menu li.active .text {
	color: white;
  }
  
  /* Amélioration des icônes */
  .bx {
	font-size: 1.4rem;
	margin-right: 10px;
	vertical-align: middle;
  }
  
  /* Style pour le lien de feedback */
  .bxs-message-alt-detail {
	color: #4CAF50; /* Couleur verte pour les feedbacks */
  }
  </style>