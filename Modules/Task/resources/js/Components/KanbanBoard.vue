<template>
  <div class="kanban-wrapper overflow-x-auto">
      <div class="d-flex gap-2">
        
        <!-- Dynamic Kanban Columns -->
        <div v-for="column in columns" :key="column.id" class="kanban-column">
          <div class="card kanban-card" :style="{ backgroundColor: getTransparentColor(column.color, cardOpacity) }">
            <div class="card-body px-3 py-2">
              <div class="d-flex justify-content-between align-items-center"> 
                <div class="fs-6 fw-semibold text-black">{{ column.title.toUpperCase() }}</div>
                <span 
                  class="badge kanban-badge"
                  :style="{ backgroundColor: getTransparentColor(column.color, badgeOpacity), color: getTextColor(column.color) }"
                >
                  {{ column.tasks.length }}
                </span>
              </div>
            </div>
          </div>

          <!-- Draggable Task List -->
          <draggable 
            v-model="column.tasks" 
            group="tasks" 
            :item-key="element => element.id"
            v-bind="taskDragOptions"
            @end="handleTaskMoved"
          >
            <template #item="{ element }">
              <div class="task-card">
                <div class="fw-bold">{{ element.title }}</div>
                <div class="text-muted small">{{ element.description }}</div>
              </div>
            </template>
          </draggable>

        </div>

      </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import axios from "axios";

export default {
  components: {
    draggable,
  },
  props: {
    initialData: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      columns: [],
      cardOpacity: 0.25, // Slightly more transparent for better layering
      badgeOpacity: 0.95, // Keep it bold and visible
      colors: ["#FF6B6B", "#6BCB77", "#FFD93D", "#4D96FF", "#8338EC"], // Vibrant UI/UX optimized colors
    };
  },
  computed: {
    taskDragOptions() {
      return {
        animation: 250,
        group: "tasks",
        ghostClass: "dragging",
      };
    },
  },
  mounted() {
    this.columns = this.initialData.map((status, index) => ({
      id: status.id,
      order: status.order,
      title: status.title,
      color: this.colors[index % this.colors.length],
      tasks: status.tasks || [],
    }));
  },
  methods: {
    handleTaskMoved() {
      axios
        .put('/admin/task/syncronize', { columns: this.columns })
        .then((response) => {
          // console.log("Tasks updated successfully:", response.data);
        })
        .catch((err) => {
          console.error("Error updating tasks:", err.response);
        });
    },
    getTransparentColor(hex, opacity) {
      const r = parseInt(hex.substring(1, 3), 16);
      const g = parseInt(hex.substring(3, 5), 16);
      const b = parseInt(hex.substring(5, 7), 16);
      return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    },
    getTextColor(bgColor) {
      const r = parseInt(bgColor.substring(1, 3), 16);
      const g = parseInt(bgColor.substring(3, 5), 16);
      const b = parseInt(bgColor.substring(5, 7), 16);
      const luminance = (r * 0.299 + g * 0.587 + b * 0.114) / 255;
      return luminance > 0.5 ? "#000" : "#FFF";
    }
  },
};
</script>

<style scoped>
/* Scrollable Container with Hidden Scrollbar */
.kanban-wrapper {
  white-space: nowrap;
  overflow-x: auto;
  padding-bottom: 10px;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* Internet Explorer */
}
.kanban-wrapper::-webkit-scrollbar {
  display: none; /* Chrome, Safari */
}

/* Kanban Column Styling */
.kanban-column {
  min-width: 18rem;
  flex: 0 0 auto;
  border-radius: 8px;
  padding: 10px;
}

/* Badge Styling */
.kanban-badge {
  padding: 6px 12px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
  color: white !important;
}

/* Task Card Styling */
.task-card {
  background: #ffffff;
  border: 1px solid #ddd;
  padding: 12px;
  margin-top: 10px;
  border-radius: 8px;
  transition: all 0.3s ease;
}
.task-card:hover {
  transform: translateY(-2px);
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.12);
}

/* Dragging Effect */
.dragging {
  opacity: 0.7;
  transform: scale(1.05);
}
</style>