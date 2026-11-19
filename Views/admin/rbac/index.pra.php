@extends("admin.layouts.admin")

@section("content")
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="rbac-page"
    id="rbac-app"
    data-users-url="{{ route('admin.rbac.users') }}"
    data-roles-url="{{ route('admin.rbac.roles') }}"
    data-permissions-url="{{ route('admin.rbac.permissions') }}"
    data-assign-role-url="{{ route('admin.rbac.users.assign-role') }}"
    data-remove-role-url="{{ route('admin.rbac.users.remove-role') }}"
    data-assign-permission-url="{{ route('admin.rbac.roles.assign-permission') }}"
    data-remove-permission-url="{{ route('admin.rbac.roles.remove-permission') }}">

    <style>
        .rbac-page { padding: 25px 0; }
        .rbac-heading { margin-bottom: 25px; }
        .rbac-heading h1 { margin-bottom: 8px; }
        .rbac-back {
            display: inline-block;
            margin-bottom: 18px;
            color: var(--primary-light);
        }
        .rbac-back:hover { color: #fff; }
        .rbac-section { margin: 28px 0; }
        .rbac-section h2 {
            margin-bottom: 15px;
            color: var(--primary-light);
        }
        .rbac-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
            gap: 14px;
        }
        .rbac-card {
            padding: 18px;
            background: var(--surface);
            border: 1px solid var(--primary-border);
            border-radius: 12px;
            min-width: 0;
        }
        .rbac-card h3 { margin: 0 0 7px; }
        .rbac-muted { color: var(--muted); overflow-wrap: anywhere; }
        .rbac-list { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0; }
        .rbac-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            background: var(--primary-soft);
            border: 1px solid var(--primary-border);
            border-radius: 7px;
            font-size: 13px;
        }
        .rbac-chip button {
            border: 0;
            color: #ff9999;
            background: transparent;
            cursor: pointer;
            padding: 0;
        }
        .rbac-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }
        .rbac-select {
            flex: 1;
            min-width: 0;
            padding: 11px;
            color: #fff;
            background: #050505;
            border: 1px solid var(--border);
            border-radius: 7px;
            outline: none;
        }
        .rbac-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }
        .rbac-btn {
            padding: 10px 13px;
            color: #fff;
            background: var(--primary);
            border: 1px solid var(--primary);
            border-radius: 7px;
            font-weight: bold;
            cursor: pointer;
            transition: background .2s ease, border-color .2s ease;
        }
        .rbac-btn:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        .rbac-btn:focus-visible {
            outline: 2px solid var(--primary-light);
            outline-offset: 3px;
        }
        .rbac-btn:disabled { opacity: .45; cursor: not-allowed; }
        .rbac-notice {
            display: none;
            margin: 15px 0;
            padding: 12px;
            border: 1px solid #444;
            border-radius: 8px;
            overflow-wrap: anywhere;
        }
        .rbac-notice.show { display: block; }
        .rbac-notice.error { color: #ff9999; border-color: #733; }
        .rbac-notice.success { color: #b8f5c5; border-color: #365b3e; }
        .rbac-empty { color: #999; padding: 12px 0; }
        @media(max-width:520px) {
            .rbac-actions { flex-direction: column; }
            .rbac-btn { width: 100%; }
        }
    </style>

    <a class="rbac-back" href="{{ route('admin.dashboard') }}">← Admin Dashboard</a>

    <div class="rbac-heading">
        <h1>RBAC Management</h1>
        <p class="admin-muted">Manage users, roles and permissions.</p>
    </div>

    <div id="rbac-notice" class="rbac-notice" role="status" aria-live="polite"></div>
    <p id="rbac-loading" class="admin-muted">Loading RBAC data...</p>

    <section class="rbac-section">
        <h2>Users & Roles</h2>
        <div id="rbac-users" class="rbac-grid"></div>
    </section>

    <section class="rbac-section">
        <h2>Roles & Permissions</h2>
        <div id="rbac-roles" class="rbac-grid"></div>
    </section>

    <section class="rbac-section">
        <h2>Available Permissions</h2>
        <div id="rbac-permissions" class="rbac-grid"></div>
    </section>
</section>

<script>
(() => {
    const app = document.getElementById('rbac-app');
    if (!app) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const notice = document.getElementById('rbac-notice');
    const loading = document.getElementById('rbac-loading');

    const urls = {
        users: app.dataset.usersUrl,
        roles: app.dataset.rolesUrl,
        permissions: app.dataset.permissionsUrl,
        assignRole: app.dataset.assignRoleUrl,
        removeRole: app.dataset.removeRoleUrl,
        assignPermission: app.dataset.assignPermissionUrl,
        removePermission: app.dataset.removePermissionUrl
    };

    let users = [];
    let roles = [];
    let permissions = [];

    function element(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    function showNotice(message, type = 'success') {
        notice.textContent = message;
        notice.className = 'rbac-notice show ' + type;
    }

    function hideNotice() {
        notice.textContent = '';
        notice.className = 'rbac-notice';
    }

    async function request(url, options = {}) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options,
            headers: {
                Accept: 'application/json',
                ...(options.headers || {})
            }
        });

        const result = await response.json().catch(() => null);

        if (!response.ok || !result || result.success !== true) {
            throw new Error(result?.message || `Request failed (${response.status})`);
        }

        return result.data;
    }

    async function post(url, payload) {
        return request(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(payload)
        });
    }

    function makeSelect(items, placeholder, getId, getLabel) {
        const select = element('select', 'rbac-select');
        const first = element('option', '', placeholder);
        first.value = '';
        select.appendChild(first);

        items.forEach(item => {
            const option = element('option', '', getLabel(item));
            option.value = getId(item);
            select.appendChild(option);
        });

        return select;
    }

    function makeButton(label, callback) {
        const button = element('button', 'rbac-btn', label);
        button.type = 'button';
        button.addEventListener('click', callback);
        return button;
    }

    async function perform(url, payload, successMessage) {
        hideNotice();

        try {
            await post(url, payload);
            showNotice(successMessage, 'success');
            await loadData();
        } catch (error) {
            showNotice(error.message, 'error');
        }
    }

    function renderUsers() {
        const container = document.getElementById('rbac-users');
        container.replaceChildren();

        if (!users.length) {
            container.appendChild(element('p', 'rbac-empty', 'No users found.'));
            return;
        }

        users.forEach(user => {
            const card = element('article', 'rbac-card');
            card.appendChild(element('h3', '', user.name || 'Unnamed User'));
            card.appendChild(element('p', 'rbac-muted', user.email || ''));
            card.appendChild(element('small', 'rbac-muted', `User ID: ${user.id}`));

            const roleList = element('div', 'rbac-list');

            if (!user.roles?.length) {
                roleList.appendChild(element('span', 'rbac-empty', 'No roles assigned.'));
            } else {
                user.roles.forEach(role => {
                    const chip = element('span', 'rbac-chip', role.name);
                    const remove = element('button', '', '×');
                    remove.type = 'button';
                    remove.title = 'Remove role';
                    remove.addEventListener('click', () => {
                        perform(urls.removeRole, {
                            user_id: user.id,
                            role_id: role.id
                        }, 'Role removed successfully.');
                    });
                    chip.appendChild(remove);
                    roleList.appendChild(chip);
                });
            }

            card.appendChild(roleList);

            const availableRoles = roles.filter(
                role => !(user.roles || []).some(existing => Number(existing.id) === Number(role.id))
            );

            const select = makeSelect(
                availableRoles,
                'Select a role',
                role => role.id,
                role => `${role.name} (${role.slug})`
            );

            const assign = makeButton('Assign Role', () => {
                if (!select.value) {
                    showNotice('Please select a role first.', 'error');
                    return;
                }

                perform(urls.assignRole, {
                    user_id: user.id,
                    role_id: Number(select.value)
                }, 'Role assigned successfully.');
            });

            const actions = element('div', 'rbac-actions');
            actions.append(select, assign);
            card.appendChild(actions);
            container.appendChild(card);
        });
    }

    function renderRoles() {
        const container = document.getElementById('rbac-roles');
        container.replaceChildren();

        if (!roles.length) {
            container.appendChild(element('p', 'rbac-empty', 'No roles found.'));
            return;
        }

        roles.forEach(role => {
            const card = element('article', 'rbac-card');
            card.appendChild(element('h3', '', role.name));
            card.appendChild(element('p', 'rbac-muted', role.description || role.slug));

            const permissionList = element('div', 'rbac-list');

            if (!role.permissions?.length) {
                permissionList.appendChild(element('span', 'rbac-empty', 'No permissions assigned.'));
            } else {
                role.permissions.forEach(permission => {
                    const chip = element('span', 'rbac-chip', permission.name);
                    const remove = element('button', '', '×');
                    remove.type = 'button';
                    remove.title = 'Remove permission';
                    remove.addEventListener('click', () => {
                        perform(urls.removePermission, {
                            role_id: role.id,
                            permission_id: permission.id
                        }, 'Permission removed successfully.');
                    });
                    chip.appendChild(remove);
                    permissionList.appendChild(chip);
                });
            }

            card.appendChild(permissionList);

            const availablePermissions = permissions.filter(
                permission => !(role.permissions || []).some(
                    existing => Number(existing.id) === Number(permission.id)
                )
            );

            const select = makeSelect(
                availablePermissions,
                'Select a permission',
                permission => permission.id,
                permission => `${permission.name} (${permission.slug})`
            );

            const assign = makeButton('Assign Permission', () => {
                if (!select.value) {
                    showNotice('Please select a permission first.', 'error');
                    return;
                }

                perform(urls.assignPermission, {
                    role_id: role.id,
                    permission_id: Number(select.value)
                }, 'Permission assigned successfully.');
            });

            const actions = element('div', 'rbac-actions');
            actions.append(select, assign);
            card.appendChild(actions);
            container.appendChild(card);
        });
    }

    function renderPermissions() {
        const container = document.getElementById('rbac-permissions');
        container.replaceChildren();

        if (!permissions.length) {
            container.appendChild(element('p', 'rbac-empty', 'No permissions found.'));
            return;
        }

        permissions.forEach(permission => {
            const card = element('article', 'rbac-card');
            card.appendChild(element('h3', '', permission.name));
            card.appendChild(element('p', 'rbac-muted', permission.slug));
            if (permission.description) {
                card.appendChild(element('p', 'rbac-muted', permission.description));
            }
            card.appendChild(element('small', 'rbac-muted', `Permission ID: ${permission.id}`));
            container.appendChild(card);
        });
    }

    async function loadData() {
        loading.hidden = false;

        try {
            const results = await Promise.all([
                request(urls.users),
                request(urls.roles),
                request(urls.permissions)
            ]);

            users = results[0] || [];
            roles = results[1] || [];
            permissions = results[2] || [];

            renderUsers();
            renderRoles();
            renderPermissions();
        } catch (error) {
            showNotice(error.message, 'error');
        } finally {
            loading.hidden = true;
        }
    }

    loadData();
})();
</script>
@endsection
