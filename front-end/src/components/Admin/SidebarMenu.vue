<!-- SidebarMenu.vue -->
<template>
	<aside id="sidebar">
	  <!-- Sidebar content goes here -->
	<section id="sidebar">
		  <a href="#" class="brand">
			  <i class='bx bxs-cog'></i>
			  <span class="text">Admin</span>
		  </a>
		  <ul class="side-menu top">
			  <li class="active">
				  <a href="#">
					  <i class='bx bxs-dashboard'></i>
					  <router-link to="/Admin"><span class="text">Tableau de bord</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-user-badge'></i>
					  <router-link to="/TuteursListAdmin"><span class="text">Les Tuteurs</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-file'></i>
					  <router-link to="/TuteursRequests"><span class="text">Demande Tuteurs</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-category'></i>
					  <router-link to="/CategoryList"><span class="text">Category</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-certification'></i>
					  <router-link to="/SpecialiteList"><span class="text">Specialité</span></router-link>
				  </a>
			  </li>
			  <!-- Modifié pour l'icône Domaine -->
			<li>
			<a href="#">
				<i class='bx bxs-layer'></i>
				<router-link to="/DomaineList"><span class="text">Domaine</span></router-link>
			</a>
			</li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-graduation'></i>
					  <router-link to="/NiveauList"><span class="text">Niveau</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-factory'></i>
					  <router-link to="/SecteurList"><span class="text">Secteur</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-briefcase-alt'></i>
					  <router-link to="/OffresListAdmin"><span class="text">Les offres</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-building'></i>
					  <router-link to="/UsersList"><span class="text">Les entreprises</span></router-link>
				  </a>
			  </li>
			  <li>
				  <a href="#">
					  <i class='bx bxs-user'></i>
					  <router-link to="/UsersListEtudiants"><span class="text">Les étudiants</span></router-link>
				  </a>
			  </li>
			  <li :class="{ active: $route.path === '/AdminFeedbacks' }">
				<a href="#">
				  <i class='bx bxs-message-alt-detail'></i>
				  <router-link to="/AdminFeedbacks"><span class="text">Feedbacks</span></router-link>
				</a>
			  </li>
		  </ul>
		  <ul class="side-menu">
			  <li>
				  <a href="#" class="logout">
					  <i class='bx bxs-log-out-circle'></i>
					  <router-link to="/signin"><span class="text">Déconnexion</span></router-link>
				  </a>
			  </li>
		  </ul>
	  </section>
	</aside>
</template>

<script>
export default {
	// SidebarMenu component logic
}
</script>

<style scoped>
</style>