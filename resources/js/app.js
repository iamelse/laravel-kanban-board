import { createApp } from 'vue';
import KanbanBoard from '../../Modules/Task/resources/js/Components/KanbanBoard.vue';

const app = createApp({});
app.component('kanban-board', KanbanBoard);
app.mount('#app');

document.addEventListener("DOMContentLoaded", function () {
    const burgerBtn = document.querySelector(".burger-btn");
    const sidebar = document.getElementById("sidebar");

    if (burgerBtn && sidebar) {
        burgerBtn.addEventListener("click", function (event) {
            event.preventDefault();
            sidebar.classList.toggle("active");
            sidebar.classList.toggle("inactive");
        });
    }
});