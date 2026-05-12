let currentUser = null;
let currentPath = 'dashboard';
let globalGroupIdFilter = null;

function enterGroup(groupId) {
    globalGroupIdFilter = groupId;
    loadPage('sessions');
}

function isGroupAdmin(groupId) {
    if (!currentUser || !groupId) return false;
    if (currentUser.role === 'Admin') return true;
    if (!currentUser.admin_groups) return false;
    return currentUser.admin_groups.some(g => g.group_id && g.group_id.toString() === groupId.toString());
}

const routes = [
    { path: 'dashboard', name: 'Dashboard', icon: 'layout-dashboard', file: '/pages/dashboard.html' },
    { path: 'students', name: 'Students', icon: 'users', file: '/pages/students.html' },
    { path: 'groups', name: 'Study Groups', icon: 'users-2', file: '/pages/groups.html' },
    { path: 'join_requests', name: 'Join Requests', icon: 'user-plus', file: '/pages/join_requests.html' },
    { path: 'members', name: 'Members', icon: 'user-check', file: '/pages/members.html' },
    { path: 'resources', name: 'Resources', icon: 'file-text', file: '/pages/resources.html' },
    { path: 'sessions', name: 'Sessions', icon: 'calendar', file: '/pages/sessions.html' },
    { path: 'attendance', name: 'Attendance', icon: 'clipboard-check', file: '/pages/attendance.html' },
    { path: 'leaderboard', name: 'Leaderboard', icon: 'trophy', file: '/pages/leaderboard.html' },
    { path: 'admin', name: 'Admin Panel', icon: 'shield-check', file: '/pages/admin.html', adminOnly: true }
];

document.addEventListener('DOMContentLoaded', async () => {
    initTheme();
    lucide.createIcons();

    const session = await apiCall('/api/auth/session.php');
    if (session.success) {
        currentUser = session.user;
        showDashboard();
    } else {
        showLoginScreen();
    }
});

function showLoginScreen() {
    document.getElementById('login-screen').classList.remove('hidden');
    document.getElementById('dashboard-shell').classList.add('hidden');

    const form = document.getElementById('login-form');
    form.onsubmit = async (e) => {
        e.preventDefault();
        const errBox = document.getElementById('login-error');
        const btn = document.getElementById('login-btn');
        errBox.classList.add('hidden');
        btn.disabled = true;
        btn.textContent = 'Signing in...';

        const formData = new FormData();
        formData.append('student_id', document.getElementById('login-id').value.trim());
        formData.append('password', document.getElementById('login-pass').value);

        const data = await apiCall('/api/auth/login.php', { method: 'POST', body: formData });

        btn.disabled = false;
        btn.textContent = 'Sign In';

        if (data.success) {
            currentUser = data.user;
            showDashboard();
        } else {
            errBox.textContent = data.message;
            errBox.classList.remove('hidden');
        }
    };

    const regForm = document.getElementById('register-form');
    if (regForm) {
        regForm.onsubmit = async (e) => {
            e.preventDefault();
            const errBox = document.getElementById('register-error');
            const btn = document.getElementById('register-btn');
            errBox.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Creating Account...';

            const formData = new FormData();
            formData.append('student_id', document.getElementById('reg-id').value.trim());
            formData.append('name', document.getElementById('reg-name').value.trim());
            formData.append('gsuite_email', document.getElementById('reg-email').value.trim());
            formData.append('department', document.getElementById('reg-dept').value.trim());
            formData.append('password', document.getElementById('reg-pass').value);

            const data = await apiCall('/api/student/create.php', { method: 'POST', body: formData });

            if (data.success) {
                const loginData = new FormData();
                loginData.append('student_id', document.getElementById('reg-id').value.trim());
                loginData.append('password', document.getElementById('reg-pass').value);
                const sessionData = await apiCall('/api/auth/login.php', { method: 'POST', body: loginData });
                
                if (sessionData.success) {
                    currentUser = sessionData.user;
                    showDashboard();
                } else {
                    toggleAuthForm();
                    showToast('Account created! Please log in.', 'success');
                }
            } else {
                errBox.textContent = data.message;
                errBox.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Create Account';
            }
        };
    }
}

function toggleAuthForm() {
    const loginContainer = document.getElementById('login-form-container');
    const regContainer = document.getElementById('register-form-container');
    const authHeading = document.querySelector('#login-screen p.text-blue-200');
    
    if (regContainer.classList.contains('hidden')) {
        regContainer.classList.remove('hidden');
        authHeading.textContent = 'Create your account';
        
        setTimeout(() => {
            loginContainer.classList.replace('translate-x-0', '-translate-x-full');
            regContainer.classList.replace('translate-x-full', 'translate-x-0');
            setTimeout(() => loginContainer.classList.add('hidden'), 300);
        }, 10);
    } else {
        loginContainer.classList.remove('hidden');
        authHeading.textContent = 'Sign in to your dashboard';
        
        setTimeout(() => {
            regContainer.classList.replace('translate-x-0', 'translate-x-full');
            loginContainer.classList.replace('-translate-x-full', 'translate-x-0');
            setTimeout(() => regContainer.classList.add('hidden'), 300);
        }, 10);
    }
}

function showDashboard() {
    document.getElementById('login-screen').classList.add('hidden');
    document.getElementById('dashboard-shell').classList.remove('hidden');

    updateUserUI();
    renderSidebar();
    setupUserMenu();
    setupProfileForm();

    const hash = window.location.hash.replace('#', '');
    if (hash && routes.some(r => r.path === hash)) {
        currentPath = hash;
    }

    loadPage(currentPath);
    lucide.createIcons();
}

function updateUserUI() {
    if (!currentUser) return;
    const initials = currentUser.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    document.getElementById('user-avatar').textContent = initials;
    document.getElementById('user-display-name').textContent = currentUser.name;
    document.getElementById('dropdown-name').textContent = currentUser.name;
    document.getElementById('dropdown-email').textContent = currentUser.email;
    document.getElementById('dropdown-role').textContent = currentUser.role;
}

function setupUserMenu() {
    const btn = document.getElementById('user-menu-btn');
    const dropdown = document.getElementById('user-dropdown');

    btn.onclick = (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
    };

    document.addEventListener('click', () => {
        dropdown.classList.add('hidden');
    });

    dropdown.onclick = (e) => e.stopPropagation();
}

function openProfileEditor() {
    document.getElementById('user-dropdown').classList.add('hidden');
    document.getElementById('profile-id').value = currentUser.student_id;
    document.getElementById('profile-name').value = currentUser.name;
    document.getElementById('profile-email').value = currentUser.email;
    document.getElementById('profile-dept').value = currentUser.department;
    document.getElementById('profile-password').value = '';
    document.getElementById('profile-modal').classList.remove('hidden');
    lucide.createIcons();
}

function setupProfileForm() {
    const form = document.getElementById('profile-form');
    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('name', document.getElementById('profile-name').value);
        formData.append('gsuite_email', document.getElementById('profile-email').value);
        formData.append('department', document.getElementById('profile-dept').value);
        const pw = document.getElementById('profile-password').value;
        if (pw) formData.append('new_password', pw);

        const data = await apiCall('/api/auth/update_profile.php', { method: 'POST', body: formData });

        if (data.success) {
            currentUser = data.user;
            updateUserUI();
            document.getElementById('profile-modal').classList.add('hidden');
            showToast('Profile updated successfully', 'success');
        } else {
            showToast(data.message || 'Update failed', 'error');
        }
    };
}

async function handleLogout() {
    await apiCall('/api/auth/logout.php');
    currentUser = null;
    showLoginScreen();
}

function initTheme() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const darkIcon = document.getElementById('theme-icon-dark');
    const lightIcon = document.getElementById('theme-icon-light');

    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        lightIcon.classList.remove('hidden');
    } else {
        document.documentElement.classList.remove('dark');
        darkIcon.classList.remove('hidden');
    }

    themeToggleBtn.addEventListener('click', () => {
        darkIcon.classList.toggle('hidden');
        lightIcon.classList.toggle('hidden');

        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
    });
}

function renderSidebar() {
    const nav = document.getElementById('sidebar-nav');
    const visibleRoutes = routes.filter(r => !r.adminOnly || (currentUser && currentUser.role === 'Admin'));

    nav.innerHTML = visibleRoutes.map(route => `
        <a href="#${route.path}"
           onclick="loadPage('${route.path}')"
           class="nav-link flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ${currentPath === route.path ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'}"
           data-path="${route.path}">
            <i data-lucide="${route.icon}" class="w-5 h-5 mr-3"></i>
            ${route.name}
        </a>
    `).join('');
    lucide.createIcons();
}

async function loadPage(path) {
    currentPath = path;
    window.location.hash = path;
    const route = routes.find(r => r.path === path) || routes[0];

    document.getElementById('page-title').innerText = route.name;

    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('data-path') === path) {
            link.className = `nav-link flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400`;
        } else {
            link.className = `nav-link flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700`;
        }
    });

    const loader = document.getElementById('loader');
    const view = document.getElementById('app-view');

    loader.classList.remove('hidden');

    try {
        const response = await fetch(route.file);
        if (!response.ok) throw new Error('Page not found');
        const html = await response.text();
        view.innerHTML = html;

        lucide.createIcons();

        const scripts = view.querySelectorAll('script');
        scripts.forEach(script => {
            const newScript = document.createElement('script');
            newScript.textContent = script.textContent;
            document.body.appendChild(newScript);
            document.body.removeChild(newScript);
        });

    } catch (err) {
        view.innerHTML = `<div class="p-4 text-red-500 bg-red-100 dark:bg-red-900/20 rounded border border-red-200 dark:border-red-800">Failed to load page: ${err.message}</div>`;
    } finally {
        loader.classList.add('hidden');
    }
}

async function populateMyGroupsDropdown(selectId, currentValue = '') {
    const groupSelect = document.getElementById(selectId);
    if (!groupSelect) return;
    
    const originalFirstOpt = groupSelect.options[0].textContent;
    groupSelect.options[0].textContent = 'Loading Groups...';
    
    const session = await apiCall('/api/auth/session.php');
    if (session.success) {
        currentUser = session.user;
    }
    
    groupSelect.options[0].textContent = originalFirstOpt;

    while (groupSelect.options.length > 1) groupSelect.remove(1);

    if (currentUser && currentUser.enrolled_groups && currentUser.enrolled_groups.length > 0) {
        currentUser.enrolled_groups.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.group_id;
            opt.textContent = g.group_name;
            if (g.group_id.toString() === currentValue.toString()) opt.selected = true;
            groupSelect.appendChild(opt);
        });
    }
}

async function apiCall(endpoint, options = {}) {
    try {
        const response = await fetch(endpoint, options);
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return await response.json();
        } else {
            const text = await response.text();
            console.error('Non-JSON response from', endpoint, ':', text);
            return { success: false, message: 'Server error: Invalid response format' };
        }
    } catch (err) {
        console.error('API Call failed:', err);
        return { success: false, message: err.message };
    }
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');

    const bgColor = type === 'success' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20';
    const borderColor = type === 'success' ? 'border-green-200 dark:border-green-800' : 'border-red-200 dark:border-red-800';
    const textColor = type === 'success' ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400';
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';

    toast.className = `flex items-center w-full max-w-xs p-4 mb-4 rounded-lg shadow border transition-all duration-300 transform translate-y-0 opacity-100 ${bgColor} ${borderColor} ${textColor}`;

    toast.innerHTML = `
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
            <i data-lucide="${icon}"></i>
        </div>
        <div class="ms-3 text-sm font-normal">${message}</div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 p-1.5 inline-flex items-center justify-center h-8 w-8 rounded-lg focus:ring-2 focus:ring-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.parentElement.remove()">
            <span class="sr-only">Close</span>
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;

    container.appendChild(toast);
    lucide.createIcons({ root: toast });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

async function handleFormSubmit(event, endpoint, onSuccess) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="animate-spin inline mr-2 w-4 h-4"></i> Processing...';
    submitBtn.disabled = true;
    lucide.createIcons();

    const data = await apiCall(endpoint, {
        method: 'POST',
        body: formData
    });

    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;

    if (data.success) {
        showToast(data.message, 'success');
        form.reset();
        
        const session = await apiCall('/api/auth/session.php');
        if (session.success) currentUser = session.user;

        if (onSuccess) onSuccess();
    } else {
        showToast(data.message || 'Operation failed', 'error');
    }
}

function customConfirm(message) {
    return new Promise((resolve) => {
        const modalId = 'confirm-modal-' + Date.now();
        const html = `
        <div id="${modalId}" class="fixed inset-0 bg-gray-900/80 flex items-center justify-center z-[100]">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm overflow-hidden p-6 text-center">
                <i data-lucide="alert-triangle" class="w-12 h-12 text-yellow-500 mx-auto mb-4"></i>
                <p class="text-gray-900 dark:text-white font-medium mb-6">${message}</p>
                <div class="flex justify-center gap-3">
                    <button id="${modalId}-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">Cancel</button>
                    <button id="${modalId}-confirm" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Confirm</button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        lucide.createIcons();

        document.getElementById(`${modalId}-cancel`).onclick = () => {
            document.getElementById(modalId).remove();
            resolve(false);
        };
        document.getElementById(`${modalId}-confirm`).onclick = () => {
            document.getElementById(modalId).remove();
            resolve(true);
        };
    });
}

function customPrompt(message, defaultValue = '') {
    return new Promise((resolve) => {
        const modalId = 'prompt-modal-' + Date.now();
        const html = `
        <div id="${modalId}" class="fixed inset-0 bg-gray-900/80 flex items-center justify-center z-[100]">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm overflow-hidden p-6">
                <p class="text-gray-900 dark:text-white font-medium mb-4">${message}</p>
                <input type="text" id="${modalId}-input" value="${defaultValue}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 mb-6">
                <div class="flex justify-end gap-3">
                    <button id="${modalId}-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">Cancel</button>
                    <button id="${modalId}-confirm" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">Submit</button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);

        document.getElementById(`${modalId}-cancel`).onclick = () => {
            document.getElementById(modalId).remove();
            resolve(null);
        };
        document.getElementById(`${modalId}-confirm`).onclick = () => {
            const val = document.getElementById(`${modalId}-input`).value;
            document.getElementById(modalId).remove();
            resolve(val);
        };
    });
}
