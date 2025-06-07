import { createRouter, createWebHistory } from "vue-router";
import Signin from "../components/Auth/SignIn.vue";
import SignupEtudiant from "../components/Auth/SignUpEtudiant.vue";
import SignupEtudiantPart2 from "../components/Auth/SignUpEtudiantPart2.vue";
import SignupEntreprise from "../components/Auth/SignUpEntreprise.vue";
import SignupEntreprisePart2 from "../components/Auth/SignUpEntreprisePart2.vue";

import SignUpTuteur from "../components/Auth/SignUpTuteur.vue";
import SignUpTuteurPart2 from "../components/Auth/SignUpTuteurPart2.vue";

import Contact from "../components/Contact.vue";
import LandingPage from "../components/LandingPage.vue";
import Teams from "../components/Teams.vue";

import StudentDash from "../components/StudentDash/StudentDashboard.vue";
import NavBarStd from "../components/StudentDash/NavBarStd.vue";
import DetailsOffreStd from "../components/StudentDash/DetailsOffreStd.vue";
import PostulerCondidature from "../components/StudentDash/PostulerCondidature.vue";
import ListeStagesAcceptes from "../components/StudentDash/ListeStagesAcceptes.vue";
import Consulternotif from "../components/StudentDash/Consulternotif.vue";
import EtudiantAccount from "../components/StudentDash/EtudiantAccount.vue";
import OffersListStd from "../components/StudentDash/OffersListStd.vue";
import ConsultListCours from "../components/StudentDash/ConsultListCours.vue";
import ConsultRessource from "../components/StudentDash/ConsultRessource.vue";
import DetailCour from "../components/StudentDash/DetailCour.vue";

import EntrepriseDash from "../components/EntrepriseDash/EntrepriseDashbord.vue";
import OffersList from "../components/EntrepriseDash/OffersList.vue";
import AddOffer from "../components/EntrepriseDash/AddOffer.vue";
import DetailOffre from "../components/EntrepriseDash/DétailOffre.vue";
import EditOffre from "../components/EntrepriseDash/EditOffre.vue";
import TreatedRequest from "../components/EntrepriseDash/TreatedRequest.vue";
import EntrepriseAccount from "../components/EntrepriseDash/Account.vue";
import StudentsList from "../components/EntrepriseDash/StudentsList.vue";
import DetailStudent from "../components/EntrepriseDash/DetailStudent.vue";
import DetailDemande from "../components/EntrepriseDash/DetailDemande.vue";
import EditDemande from "../components/EntrepriseDash/EditDemande.vue";

import AdminDashboard from "../components/Admin/AdminDash.vue";
import UsersListEtudiants from "../components/Admin/UsersListEtudiants.vue";
import OffresListAdmin from "../components/Admin/OffresListAdmin.vue";
import MessagesList from "../components/Admin/MessagesList.vue";
import DetailOffreAdmin from "../components/Admin/DetailOffreAdmin.vue";
import UsersList from "../components/Admin/UsersList.vue";
import AccountSetting from "../components/Admin/AccountSetting.vue";
import AddOfferAdmin from "../components/Admin/AddOffer.vue";
import CreateTuteur from "../components/Admin/CreateTuteur.vue";

import TuteurDashboard from "../components/TuteurDash/TuteurDashboard.vue";
import CoursListe from "../components/TuteurDash/CoursListe.vue";
import Cours from "../components/TuteurDash/Cours.vue";
// import Appointment from "../components/TuteurDash/apptest.vue";
import CoursEdit from "../components/TuteurDash/CoursEdit.vue";
import TuteurProfile from "../components/TuteurDash/TuteurProfile.vue";
import AppointmentView from "../components/StudentDash/AppointmentView.vue";

import AddCategory from "../components/Admin/AddCategory.vue";
import DetailCategory from "../components/Admin/DetailCategory.vue";
import UpdateCategory from "../components/Admin/UpdateCategory.vue";
import CategoryList from "../components/Admin/CategoryList.vue";
import TuteursListAdmin from "../components/Admin/TuteursListAdmin.vue";
import DetailTuteurAdmin from "../components/Admin/DetailTuteurAdmin.vue";
//import CoursListe from "../components/TuteurDash/CoursListe.vue";
//import CoursEdit from "../components/TuteurDash/CoursEdit.vue";
import CoursDetails from "../components/TuteurDash/CoursDetails.vue";
import RessourceList from "../components/TuteurDash/RessourceList.vue";
import RessourceAdd from "../components/TuteurDash/RessourceAdd.vue";
import RessourceEdit from "../components/TuteurDash/RessourceEdit.vue";

import QuizzList from "../components/TuteurDash/QuizzList.vue";
import QuizzAdd from "../components/TuteurDash/QuizzAdd.vue";
import QuizzEdit from "../components/TuteurDash/QuizzEdit.vue";
import ListeQuizz from "../components/StudentDash/ListeQuizz.vue";
import PasserQuizz from "../components/StudentDash/PasserQuizz.vue";
import QuestionList from "../components/TuteurDash/QuestionList.vue";


import FeedbackForm from "../components/StudentDash/FeedbackForm.vue";
import AdminFeedbacks from "../components/Admin/AdminFeedbacks.vue";
import EditFeedback from "../components/StudentDash/EditFeedback.vue";


import SpecialiteList from "../components/Admin/SpecialiteList.vue";
import AddSpecialite from "../components/Admin/AddSpecialite.vue";
import UpdateSpecialite from "../components/Admin/UpdateSpecialite.vue";
import DetailSpecialite from "../components/Admin/DetailSpecialite.vue";


import DomaineList from "../components/Admin/DomaineList.vue";
import AddDomaine from "../components/Admin/AddDomaine.vue";
import UpdateDomaine from "../components/Admin/UpdateDomaine.vue";
import DetailDomaine from "../components/Admin/DetailDomaine.vue";


import NiveauList from "../components/Admin/NiveauList.vue";
import AddNiveau from "../components/Admin/AddNiveau.vue";
import UpdateNiveau from "../components/Admin/UpdateNiveau.vue";
import DetailNiveau from "../components/Admin/DetailNiveau.vue";



import SecteurList from "../components/Admin/SecteurList.vue";
import AddSecteur from "../components/Admin/AddSecteur.vue";
import UpdateSecteur from "../components/Admin/UpdateSecteur.vue";



import TypeStageList from "../components/EntrepriseDash/TypeStageList.vue";
import AddTypeStage from "../components/EntrepriseDash/AddTypeStage.vue";
import UpdateTypeStage from "../components/EntrepriseDash/UpdateTypeStage.vue";

import Payment from "../components/StudentDash/Payment.vue";
import videoCall from "../components/video-call.vue";
import ChatView from "../components/Chat/ChatView.vue";

import notif2 from "../components/StudentDash/Consulternotif-old.vue"

import Avis from "../components/StudentDash/Avis.vue";
import AvisTut from "../components/TuteurDash/AvisTut.vue";
import TuteurRequests from "../components/Admin/TuteurRequests.vue";

import ForgotPassword from "../components/Auth/ForgotPassword.vue";
import ChangePassword from "../components/Auth/ChangePassword.vue";

import AiResume from "../components/StudentDash/AiResume.vue";  
const routes = [
  {
    path: "/",
    name: "LandingPage",
    component: LandingPage,
  },
  {
    path: "/Avis/:id",
    name: "Avis",
    component: Avis,
  },
  {
    path: "/AvisTut/:id",
    name: "AvisTuteur",
    component: AvisTut,
  },
  {
    path: "/notif2",
    name: "notif2",
    component: notif2,
  },
  {
    path: "/teams",
    name: "Teams",
    component: Teams,
  },
  {
    path: "/DetailDemande/:id",
    name: "DetailDemande",
    component: DetailDemande,
  },
  {
    path: "/video-call",
    name: "videoCall",
    component: videoCall,
  },
  {
    path: "/Payment/:id",
    name: "Payment",
    component: Payment,
  },
  {
    path: "/DetailStudent",
    name: "DetailStudent",
    component: DetailStudent,
  },
  {
    path: "/AppointVideoCall",
    name: "AppointVideoCall",
    component: () => import("../components/TuteurDash/AppointVideoCall.vue"),
  },
  {
    path: "/EntrepriseAccount",
    name: "EntrepriseAccount",
    component: EntrepriseAccount,
  },
  {
    path: "/StudentsList",
    name: "StudentsList",
    component: StudentsList,
  },
  {
    path: "/EditOffre/:idEntreprise/:id",
    name: "EditOffre",
    component: EditOffre,
  },
  {
    path: "/TreatedRequest",
    name: "TreatedRequest",
    component: TreatedRequest,
  },
  {
    path: "/detailoffre/:id",
    name: "DetailOffre",
    component: DetailOffre,
  },
  {
    path: "/EditDemande/:id",
    name: "EditDemande",
    component: EditDemande,
  },
  {
    path: "/AddOffer",
    name: "AddOffer",
    component: AddOffer,
  },
  {
    path: "/TuteursRequests",
    name: "TuteurRequests",
    component: TuteurRequests,
  },
  {
    path: "/EntrepriseDash",
    name: "EntrepriseDash",
    component: EntrepriseDash,
  },
  {
    path: "/signupEtudiant",
    name: "signupEtudiant",
    component: SignupEtudiant,
  },
  {
    path: "/OffersList",
    name: "OffersList",
    component: OffersList,
  },
  {
    path: "/signupEtudiantPart2",
    name: "signupEtudiantPart2",
    component: SignupEtudiantPart2,
  },
  {
    path: "/contact",
    name: "contact",
    component: Contact,
  },
  {
    path: "/signupEntreprise",
    name: "signupEntreprise",
    component: SignupEntreprise,
  },
  {
    path: "/signupEntreprisePart2",
    name: "signupEntreprisePart2",
    component: SignupEntreprisePart2,
  },
  {
    path: "/SignUpTuteur",
    name: "SignUpTuteur",
    component: SignUpTuteur,
  },
  {
    path: "/SignUpTuteurPart2",
    name: "SignUpTuteurPart2",
    component: SignUpTuteurPart2,
  },

  {
    path: "/signin",
    name: "signin",
    component: Signin,
  },
  {
    path: "/StudentDash",
    name: "StudentDash",
    component: StudentDash,
  },
  {
    path: "/EtudiantAccount",
    name: "EtudiantAccount",
    component: EtudiantAccount,
  },
  {
    path: '/TuteurProfile',
    name: 'TuteurProfile',
    component: TuteurProfile,
  },
  {
    path: "/Consulternotif",
    name: "Consulternotif",
    component: Consulternotif,
  },
  {
    path: "/ListeStagesAcceptes",
    name: "ListeStagesAcceptés",
    component: ListeStagesAcceptes,
  },
  {
    path: "/PostulerCondidature/:id",
    name: "PostulerCondidature",
    component: PostulerCondidature,
  },
  {
    path: "/DetailsOffreStd/:id",
    name: "DetailsOffreStd",
    component: DetailsOffreStd,
  },
  {
    path: "/OffersListStd",
    name: "OffersListStd",
    component: OffersListStd,
  },
  {
    path: "/NavBarStd",
    name: "NavBarStd",
    component: NavBarStd,
  },
  {
    path: "/Admin",
    name: "admin",
    component: AdminDashboard,
  },
  {
    path: "/OffresListAdmin",
    name: "OffresListAdmin",
    component: OffresListAdmin,
  },
  {
    path: "/MessagesList",
    name: "MessagesList",
    component: MessagesList,
  },
  {
    path: "/UsersList",
    name: "UsersList",
    component: UsersList,
  },
  {
    path: "/AccountSetting",
    name: "AccountSetting",
    component: AccountSetting,
  },
  {
    path: "/AddOfferAdmin",
    name: "AddOfferAdmin",
    component: AddOfferAdmin,
  },
  {
    path: "/DetailOffreAdmin/:id",
    name: "DetailOffreAdmin",
    component: DetailOffreAdmin,
  },
  {
    path: "/UsersListEtudiants",
    name: "UsersListEtudiants",
    component: UsersListEtudiants,
  },
  {
    path: "/TuteurDashboard",
    name: "TuteurDashboard",
    component: TuteurDashboard,
  },
  {
    path: "/tutor-chat",
    name: "TutorChat",
    component: ChatView,
    props: { userRole: 'tutor' },
    meta: { requiresAuth: true, role: 'tutor' }
  },
  {
    path: "/student-chat",
    name: "StudentChat",
    component: ChatView,
    props: { userRole: 'student' },
    meta: { requiresAuth: true, role: 'student' }
  },
  {
    path: "/CreateTuteur",
    name: "CreateTuteur",
    component: CreateTuteur,
  },
  {
    path: "/AddCategory",
    name: "AddCategory",
    component: AddCategory,
  },
  {
    path: "/DetailCategory",
    name: "DetailCategory",
    component: DetailCategory,
  },
  {
    path: "/UpdateCategory/:id",
    name: "UpdateCategory",
    component: UpdateCategory,
  },
  {
    path: "/CategoryList",
    name: "CategoryList",
    component: CategoryList,
  },
  {
    path: "/cours", // Route pour afficher la liste des cours
    name: "CoursListe",
    component: CoursListe,
  },
  {
    path: "/ajouter-cours",
    name: "AjouterCours",
    component: Cours,
  },

  {
    path: "/modifier-cours/:id",
    name: "CoursEdit",
    component: CoursEdit,
  },

  {
    path: "/ConsultListCours",
    name: "ConsultListCours",
    component: ConsultListCours,
  },
  {
    path: "/AppointmentView",
    name: "AppointmentView",
    component: AppointmentView,
  },
  {
    path: "/TuteursListAdmin",
    name: "TuteursListAdmin",
    component: TuteursListAdmin,
  },
  {
    path: "/DetailTuteurAdmin/:id",
    name: "DetailTuteurAdmin",
    component: DetailTuteurAdmin,
  },

  {
    path: "/cours/:id",
    name: "CoursDetails",
    component: CoursDetails,
  },

  {
    path: "/ajouter-ressource/:id",
    name: "RessourceList",
    component: RessourceList,
  },
  {
    path: "/cours/:id/ajouter-ressource",
    name: "RessourceAdd",
    component: RessourceAdd,
  },
  {
    path: "/modifier-ressource/:id",
    name: "RessourceEdit",
    component: RessourceEdit,
  },

  {
    path: "/DetailsCours/:id",
    name: "ConsultRessource",
    component: ConsultRessource,
  },
  {
    path: "/DetailCour/:id",
    name: "DetailCour",
    component: DetailCour,
  },
  {
    path: "/QuizzList",
    name: "QuizzList",
    component: QuizzList,
  },
  {
    path: "/QuizzAdd",
    name: "QuizzAdd",
    component: QuizzAdd,
  },
  {
    path: "/QuizzEdit/:id",
    name: "QuizzEdit",
    component: QuizzEdit,
  },

  {
    path: "/DetailsCours/:idCours/quizz",
    name: "ListeQuizz",
    component: ListeQuizz,
    props: true,
  },
  {
    path: "/quizz/:idCours/:titreQuizz",
    name: "PasserQuizz",
    component: PasserQuizz,
    props: true,
  },



  {
    path: '/cours/:idCours/feedback',
    name: 'FeedbackForm',
    component:FeedbackForm,
    props: true
  },

  {
    path: '/AdminFeedbacks',
    name: 'AdminFeedbacks',
    component: AdminFeedbacks,
    meta: { requiresAuth: true, role: 'admin' }
  },

  {
    path: '/cours/:idCours/feedback/:id/edit',
    name: 'EditFeedback',
    component:EditFeedback,
    props: true
  },

  {
    path: '/question-list/:idCours/:titre',
    name: 'QuestionList',
    component: QuestionList,
    props: true
  },

  {
    path: '/SpecialiteList',
    name: 'SpecialiteList',
    component: SpecialiteList
  },
  {
    path: '/AddSpecialite',
    name: 'AddSpecialite',
    component: AddSpecialite
  },
  {
    path: '/UpdateSpecialite/:id',
    name: 'UpdateSpecialite',
    component: UpdateSpecialite
  },
  {
    path: '/DetailSpecialite/:id',
    name: 'DetailSpecialite',
    component: DetailSpecialite
  },
  {
    path: '/DomaineList',
    name: 'DomaineList',
    component: DomaineList
  },
  {
    path: '/AddDomaine',
    name: 'AddDomaine',
    component: AddDomaine
  },
  {
    path: '/UpdateDomaine/:id',
    name: 'UpdateDomaine',
    component: UpdateDomaine,
    props: true
  },
  {
    path: '/DetailDomaine/:id',
    name: 'DetailDomaine',
    component: DetailDomaine,
    props: true
  },
  {
    path: '/NiveauList',
    name: 'NiveauList',
    component: NiveauList
  },
  {
    path: '/AddNiveau',
    name: 'AddNiveau',
    component: AddNiveau
  },
  {
    path: '/UpdateNiveau/:id',
    name: 'UpdateNiveau',
    component: UpdateNiveau,
    props: true
  },
  {
    path: '/DetailNiveau/:id',
    name: 'DetailNiveau',
    component: DetailNiveau,
    props: true
  },

  {
    path: '/SecteurList',
    name: 'SecteurList',
    component: SecteurList
  },
  {
    path: '/AddSecteur',
    name: 'AddSecteur',
    component: AddSecteur
  },
  {
    path: '/UpdateSecteur/:id',
    name: 'UpdateSecteur',
    component: UpdateSecteur
  },

  {
    path: '/TypeStageList',
    name: 'TypeStageList',
    component: TypeStageList
  },
  {
    path: '/AddTypeStage',
    name: 'AddTypeStage',
    component: AddTypeStage
  },
  {
    path: '/UpdateTypeStage/:id',
    name: 'UpdateTypeStage',
    component:UpdateTypeStage,
    props: true
  },
   {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component:ForgotPassword,
    props: true
  },
  {
    path: '/change-password',
    name: '/ChangePassword',
    component:ChangePassword,
    props: true
  },

 {
    path: '/ai-resume',
    name: '/AiResume',
    component: AiResume,
    props: true
  },

  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
