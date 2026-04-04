window.DS_Table = {
    tables: {},

    init(id, config) {
        this.tables[id] = {
            page: 1,
            search: "",
            config,
            debounceTimer: null
        };

        const table = document.getElementById(id);
        if (!table) {
            console.error(`Table with id "${id}" not found`);
            return;
        }

        // Insert Search Input above the table if not exists
        if (!document.getElementById(id + "-search")) {
            const searchWrapper = document.createElement("div");
            searchWrapper.style.marginBottom = "10px";

            const searchInput = document.createElement("input");
            searchInput.type = "search";
            searchInput.id = id + "-search";
            searchInput.placeholder = config.searchPlaceholder || "Search...";
            searchInput.classList.add(
                "m-2",
                "form-control",
                "w-75",
                "datatable-searchbox"
            );

            searchInput.setAttribute("aria-label", "Search");

            // Wire up input event with debounce
            searchInput.addEventListener("input", e => {
                this.search(id, e.target.value);
            });

            searchWrapper.appendChild(searchInput);
            table.parentNode.insertBefore(searchWrapper, table);
        }

        // Insert Pagination container below the table if not exists
        if (!document.getElementById(id + "-pagination")) {
            const pagNav = document.createElement("nav");
            pagNav.style.marginTop = "10px";
            const pagUl = document.createElement("ul");
            pagUl.id = id + "-pagination";
            pagUl.className =
                "pagination d-flex justify-content-center align-items-center";

            pagNav.appendChild(pagUl);
            table.parentNode.insertBefore(pagNav, table.nextSibling);
        }

        // Initial load
        this.load(id);
    },

    load(id) {
        const t = this.tables[id];

        fetch(
            `${t.config.ajax}?page=${t.page}&search=${encodeURIComponent(t.search)}`
        )
            .then(res => res.json())
            .then(res => {
                // 🔥 FIX HERE
                this.render(id, res.data.data);
                this.paginate(id, res.data.meta);
            })
            .catch(err => {
                console.error("Failed to load table data", err);
            });
    },

    getValueByPath(obj, path) {
        if (!path) return "";
        return path.split(".").reduce((acc, key) => {
            return acc && acc[key] !== undefined ? acc[key] : "";
        }, obj);
    },

    reload(id, resetPage = false) {
        if (!this.tables[id]) return;

        if (resetPage) this.tables[id].page = 1;
        this.load(id);
    },

    render(id, rows) {
        const tbody = document.querySelector(`#${id} tbody`);
        tbody.innerHTML = "";

        if (!rows.length) {
            tbody.innerHTML = `
      <tr>
        <td colspan="100%" class="text-center">No data found!</td>
      </tr>`;
            return;
        }

        rows.forEach(row => {
            let tr = `<tr data-id="${row.id}">`;

            Object.entries(this.tables[id].config.columns).forEach(
                ([col, colConfig]) => {
                    const label = colConfig.label || col;

                    if (typeof colConfig.render === "function") {
                        tr += `
            <td data-col="${col}" data-label="${label}" class="text-center">
              ${colConfig.render(row)}
            </td>`;
                    } else {
                        tr += `
            <td data-col="${col}" data-label="${label}">
              ${this.getValueByPath(row, colConfig)}
            </td>`;
                    }
                }
            );

            tr += "</tr>";
            tbody.innerHTML += tr;
        });
    },

    formatDateForDisplay(isoDate) {
        const date = new Date(isoDate);
        if (isNaN(date)) return "";
        const options = { day: "numeric", month: "short", year: "numeric" };
        return date.toLocaleDateString("en-US", options);
    },

    paginate(id, meta) {
        const pag = document.getElementById(id + "-pagination");
        pag.innerHTML = "";

        const current = meta.current_page;
        const total = meta.total_pages;

        function createPageItem(
            page,
            text = null,
            disabled = false,
            active = false
        ) {
            const li = document.createElement("li");
            li.className = "page-item";
            if (disabled) li.classList.add("disabled");
            if (active) li.classList.add("active");

            const a = document.createElement("a");
            a.className = "page-link";
            a.href = "#";
            a.textContent = text || page;
            if (!disabled && !active) {
                a.addEventListener("click", e => {
                    e.preventDefault();
                    DS_Table.page(id, page);
                });
            } else {
                a.tabIndex = -1;
                a.setAttribute("aria-disabled", "true");
            }
            li.appendChild(a);
            return li;
        }

        // Previous button
        pag.appendChild(createPageItem(current - 1, "Previous", current === 1));

        // Page buttons with ellipsis logic
        const pages = [];

        // Always show first page
        pages.push(1);

        // Pages before current (current - 2, current - 1)
        for (let i = current - 2; i < current; i++) {
            if (i > 1) pages.push(i);
        }

        // Current page
        if (current !== 1 && current !== total) pages.push(current);

        // Pages after current (current + 1, current + 2)
        for (let i = current + 1; i <= current + 2; i++) {
            if (i < total) pages.push(i);
        }

        // Always show last page if more than one page
        if (total > 1) pages.push(total);

        // Remove duplicates & sort
        const uniquePages = [...new Set(pages)].sort((a, b) => a - b);

        // Insert ellipsis where gap > 1
        for (let i = 0; i < uniquePages.length; i++) {
            if (i > 0 && uniquePages[i] - uniquePages[i - 1] > 1) {
                // Add ellipsis
                const ellipsisLi = document.createElement("li");
                ellipsisLi.className = "page-item disabled";
                const ellipsisSpan = document.createElement("span");
                ellipsisSpan.className = "page-link";
                ellipsisSpan.textContent = "...";
                ellipsisSpan.setAttribute("aria-hidden", "true");
                ellipsisLi.appendChild(ellipsisSpan);
                pag.appendChild(ellipsisLi);
            }

            // Add page number button
            pag.appendChild(
                createPageItem(
                    uniquePages[i],
                    null,
                    false,
                    uniquePages[i] === current
                )
            );
        }

        // Next button
        pag.appendChild(createPageItem(current + 1, "Next", current === total));
    },

    page(id, page) {
        this.tables[id].page = page;
        this.load(id);
    },

    search(id, value) {
        const t = this.tables[id];

        clearTimeout(t.debounceTimer);

        t.debounceTimer = setTimeout(() => {
            t.search = value.trim();
            t.page = 1;
            this.load(id);
        }, 300);
    }
};

/* ========= DELETE DATA ========= */
DS_Table.delete = function (tableId, url, id) {
    DS_Alert.confirm("Delete this record?", () => {
        const fd = new FormData();
        fd.append("id", id);

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content
            },
            body: fd
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    DS_Alert.toastSuccess(res.message); // ✅ FIX
                    DS_Table.reload(tableId);
                } else {
                    DS_Alert.toastError(res.message || "Delete failed");
                }
            })
            .catch(err => {
                console.error(err);
                DS_Alert.toastError("Server not response error");
            });
    });
};

/* ========= MODAL ========= */
window.DS_Modal = {
    open(id) {
        const m = $("#" + id);

        if (m) m.fadeIn();
    },
    close(id) {
        const m = $("#" + id);
        if (m) m.fadeOut();
    },
    fill(formId, data = {}) {
        Object.keys(data).forEach(key => {
            const el = document.querySelector(`#${formId} [name="${key}"]`);
            if (!el) return;

            if (el.type === "date") {
                el.value = new Date(data[key]).toISOString().slice(0, 10);
            } else {
                el.value = data[key];
            }
        });
    }
};

/* ========= ALERT ========= */
window.DS_Alert = {
    _loaded: false,
    _loading: false,

    _load(cb) {
        if (window.Swal) {
            this._loaded = true;
            cb && cb();
            return;
        }

        if (this._loading) return;
        this._loading = true;

        const s = document.createElement("script");
        s.src =
            "https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.all.min.js";
        s.onload = () => {
            this._loaded = true;
            this._loading = false;
            cb && cb();
        };
        document.head.appendChild(s);
    },

    /* ===== MODAL ALERTS ===== */
    success(msg, title = "Success") {
        this._load(() => {
            Swal.fire({ icon: "success", title, text: msg });
        });
    },

    error(msg, title = "Error") {
        this._load(() => {
            Swal.fire({ icon: "error", title, text: msg });
        });
    },

    confirm(msg, yes) {
        this._load(() => {
            Swal.fire({
                title: "Are you sure?",
                text: msg,
                icon: "warning",
                showCancelButton: true
            }).then(r => r.isConfirmed && yes());
        });
    },

    /* ===== TOAST ALERTS ===== */
    toast(msg, icon = "success") {
        this._load(() => {
            Swal.fire({
                toast: true,
                position: "top-end",
                icon,
                title: msg,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    },

    toastSuccess(msg) {
        this.toast(msg, "success");
    },

    toastError(msg) {
        this.toast(msg, "error");
    },

    toastInfo(msg) {
        this.toast(msg, "info");
    }
};

/* ========= FORM ========= */
window.DS_Form = {
    submit(formId, url, tableId = null, modalId = null, reset = true) {
        const form = document.getElementById(formId);
        if (!form) return;

        const fd = new FormData(form);

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || ""
            },
            body: fd
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    DS_Alert.toastSuccess(res.message || "Success");

                    /* 🔄 RESET FORM */
                    if (reset) {
                        form.reset();

                        // reset selects (normal + select2)
                        $(form)
                            .find("select")
                            .each(function () {
                                $(this).val("").trigger("change");
                            });
                    }

                    if (tableId) DS_Table.reload(tableId);
                    if (modalId) DS_Modal.close(modalId);
                } else {
                    DS_Alert.toastError(res.message || "Request failed");
                }
            })
            .catch(() => {
                DS_Alert.toastError("Network or server error");
            });
    }
};
