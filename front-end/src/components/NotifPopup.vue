<template>
    <!-- Notification Popup -->
    <transition name="notification-popup">
        <div v-if="showPopup" class="fixed top-5 right-5 max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden z-50">
        <div :class="getNotificationTypeClass(currentNotification.type)" class="h-2"></div>
        <div class="p-4">
            <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
                <div :class="getNotificationTypeClass(currentNotification.type)" class="w-10 h-10 rounded-full flex items-center justify-center">
                <i :class="getNotificationIcon(currentNotification.type)" class="text-white"></i>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800">{{ currentNotification.title }}</h3>
                <p class="text-gray-600 mt-1">{{ currentNotification.message }}</p>
                <div class="mt-2 text-sm text-gray-500">{{ formatDate(currentNotification.date) }}</div>
            </div>
            <button @click="closePopup" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            </div>
            <div class="mt-3 flex justify-end space-x-2">
            <router-link v-if="currentNotification.type === 'offre'" to="/OffersListStd" class="inline-flex items-center px-3 py-1 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                <span>Consulter</span>
                <i class="fas fa-chevron-right ml-2"></i>
            </router-link>
            <button v-if="currentNotification.type === 'attestation'" @click="voirAttestation(currentNotification.attestation)" class="inline-flex items-center px-3 py-1 rounded-md bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors duration-200">
                <i class="fas fa-file-pdf mr-2"></i>
                <span>Voir l'attestation</span>
            </button>
            </div>
        </div>
        </div>
    </transition>
</template>

<script>
export default {
    data() {
        return {
            showPopup: false,
            currentNotification: {},
            notifications: [],
            currentUserType: null,
            destination: null,
            Etudiant: null,
            Tuteur: null,
            Entreprise: null,
            userId: null,
            currentUser: null,
            currentEtudiant: null,
            currentTuteur: null,
            currentEntreprise: null,
            EtudiantId: null,
            TuteurId: null,
            EntrepriseId: null,
            channelUser: null,
        };
    },

    created() {
        this.initializePusher()
        this.requestNotificationPermission()
    },
    
    methods: {

        initializePusher() {

            this.currentUser = JSON.parse(sessionStorage.getItem("CurrentUser"));
            console.log(this.currentUser);

            if (this.currentUser === 'etudiant') {
                this.EtudiantId = JSON.parse(localStorage.getItem("StudentAccountInfo")).id;
                this.channelUser = `etudiant.${this.EtudiantId}`;
                console.log("etudiant channel::::",this.channelUser);
            } else if (this.currentUser === 'tuteur') {
                this.TuteurId = JSON.parse(localStorage.getItem("TuteurAccountInfo")).id;
                this.channelUser = `tuteur.${this.TuteurId}`;
                console.log("tuteur channel::::",this.channelUser);
            } else if (this.currentUser === 'entreprise') {
                this.EntrepriseId = JSON.parse(localStorage.getItem("EntrepriseAccountInfo")).id;
                this.channelUser = `entreprise.${this.EntrepriseId}`;
                console.log("entreprise channel::::",this.channelUser);
            }

            Pusher.logToConsole = true;
            
            this.pusher = new Pusher("edc2943b2a2068f8b38c", {
                cluster: "eu",
            });

            // S'abonner au canal de notification spécifique à l'utilisateur
            this.channel = this.pusher.subscribe(`notifications.${this.channelUser}`);
            
            // Écouter les événements de notification
            this.channel.bind("notification-event", (data) => {
                if (data) {
                    this.$nextTick(() => {
                        // Afficher la notification popup
                        this.showPopupNotification(data);
                        if (data.appointmentId) {
                            localStorage.setItem('appointmentId', JSON.stringify(data.appointmentId));
                        }
                    });
                    
                    // Afficher la notification du navigateur
                    this.showNotification("Nouvelle notification", {
                        body: data.message,
                        icon: "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
                        sound: "../assets/sounds/mixkit-software-interface-start-2574.mp3"
                    });
                } else {
                    console.log("Notification ignorée car destinée à un autre utilisateur");
                }
            });
        },

        showNotification(title, options) {
            if (Notification.permission === "granted") {
                new Notification(title, options);
            } else if (Notification.permission !== "denied") {
                this.requestNotificationPermission();
            }
        },
            
        requestNotificationPermission() {
            if ("Notification" in window) {
                Notification.requestPermission().then((permission) => {
                if (permission === "granted") {
                    console.log("Notification permission granted");
                }
                });
            }
        },

        getNotificationTypeClass(type) {
            const classes = {
                offre: 'bg-blue-500',
                demande: 'bg-purple-500',
                attestation: 'bg-orange-500',
                cours: 'bg-green-500',
                ressource: 'bg-yellow-500',
                appointment: 'bg-red-500'
            }
            return classes[type] || 'bg-gray-500'
        },

        getNotificationIcon(type) {
            const icons = {
                offre: 'fas fa-briefcase',
                demande: 'fas fa-file-alt',
                attestation: 'fas fa-certificate',
                cours: 'fas fa-book',
                ressource: 'fas fa-file',
                appointment: 'fas fa-calendar-alt'
            }
            return icons[type] || 'fas fa-bell'
        },
        
        showPopupNotification(notification) {
            // Vérifier si la notification est destinée à l'utilisateur actuel
            // Si la notification a un destinataire spécifique, vérifier qu'il correspond à l'utilisateur actuel
            // if (notification.userId && notification.userId !== this.userId) {
            //     console.log("Notification reçue mais destinée à un autre utilisateur");
            //     return; // Ne pas afficher la notification si elle n'est pas pour cet utilisateur
            // }
            
            // Create notification object based on type
            let notifObj = {};
            
            if (notification.type === "demande") {
                notifObj = {
                title: "Notification de votre demande stage",
                message: notification.message,
                date: notification.date,
                type: "demande"
                };
            } else if (notification.type === "offre") {
                notifObj = {
                title: "Nouvelle Offre de stage",
                message: notification.message,
                date: notification.date,
                type: "offre"
                };
            } else if (notification.type === "attestation") {
                notifObj = {
                    title: "Notification d'attestation de stage",
                    message: notification.message,
                    date: notification.date,
                    attestation: notification.attestation,
                    type: "attestation"
                };
            } else if (notification.type === "cours") {
                notifObj = {
                    title: "Notification de cours",
                    message: notification.message,
                    date: notification.date,
                    type: "cours"
                }
            } else if (notification.type === "ressource") {
                notifObj = {
                    title: "Notification de ressource",
                    message: notification.message,
                    date: notification.date,
                    type: "ressource"
                }
            } else if (notification.type === "appointment") {
                notifObj = {
                    title: "Notification de rendez-vous",
                    message: notification.message,
                    date: notification.date,
                    type: "appointment"
                }
            } else {
                // Si le type de notification n'est pas reconnu ou n'est pas destiné à cet utilisateur
                return;
            }
            
            // Set current notification and show popup
            this.currentNotification = notifObj;
            this.showPopup = true;
            
            // Auto-close popup after 5 seconds
            if (this.popupTimer) {
                clearTimeout(this.popupTimer);
            }
            
            this.popupTimer = setTimeout(() => {
                this.closePopup();
            }, 5000);
        },

        closePopup() {
            this.showPopup = false;
        },

        formatDate(dateString) {
            if (!dateString) return 'Date non disponible';
            
            const date = new Date(dateString);
            
            // Check if date is valid
            if (isNaN(date.getTime())) {
                return 'Date invalide';
            }
            
            return date.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                // hour: '2-digit',
                // minute: '2-digit'
            });
        },
    }
}
</script>

<style>
/* Animation styles for notification popup */
.notification-popup-enter-active,
.notification-popup-leave-active {
    transition: all 0.3s ease;
}

.notification-popup-enter-from,
.notification-popup-leave-to {
    transform: translateY(-30px);
    opacity: 0;
}

.notification-popup-enter-to,
.notification-popup-leave-from {
    transform: translateY(0);
    opacity: 1;
}
</style>