document.addEventListener('DOMContentLoaded', () => {
    const API_URL = 'http://api.cc.localhost';
    let categories = [];
    let courses = [];

    // Load categories and courses
    async function initialize() {
        try {
            const [categoriesData, coursesData] = await Promise.all([
                fetch(`${API_URL}/categories`).then(res => res.json()),
                fetch(`${API_URL}/courses`).then(res => res.json())
            ]);

            categories = categoriesData;
            courses = coursesData;

            renderCategories();
            renderCourses(courses);
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }

    // Render categories in sidebar
    function renderCategories() {
        const categoryList = document.getElementById('categoryList');
        const categoriesHtml = categories.map(category => {
            const courseCount = category.course_count || 0;
            return `
                <li data-id="${category.id}">
                    ${category.name} (${courseCount})
                </li>
            `;
        }).join('');

        categoryList.innerHTML = `
            <li class="active" data-id="all">All Courses</li>
            ${categoriesHtml}
        `;

        // Add click handlers
        categoryList.querySelectorAll('li').forEach(li => {
            li.addEventListener('click', async (e) => {
                const categoryId = e.target.dataset.id;
                
                // Update active state
                categoryList.querySelectorAll('li').forEach(item => {
                    item.classList.remove('active');
                });
                e.target.classList.add('active');

                // Load courses
                if (categoryId === 'all') {
                    const response = await fetch(`${API_URL}/courses`);
                    const courses = await response.json();
                    renderCourses(courses);
                } else {
                    const response = await fetch(`${API_URL}/courses?category_id=${categoryId}`);
                    const courses = await response.json();
                    renderCourses(courses);
                }
            });
        });
    }

    // Render courses grid
    function renderCourses(courses) {
        const courseGrid = document.getElementById('courseGrid');
        courseGrid.innerHTML = courses.map(course => `
            <div class="course-card">
                <h3>${course.name}</h3>
                <p>${course.description}</p>
                <span class="course-category">${course.category_name}</span>
            </div>
        `).join('');
    }

    // Start the application
    initialize();
});
