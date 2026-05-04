(function (window) {
    window.DS_Pagination = {
        /* ================= STYLE ================= */
        injectStyle() {
            if (document.getElementById("ds-pagination-style")) return;

            const style = document.createElement("style");
            style.id = "ds-pagination-style";

            style.innerHTML = `
        .ds-pagination-wrapper{
            display:flex;
            justify-content:center;
            margin-top:20px;
            margin-bottom:20px;
        }

        .pagination {
            display:flex;
            gap:6px;
            padding:10px 0;
            list-style:none;
            flex-wrap:wrap;
        }

        .page-link {
            padding:8px 12px;
            border-radius:8px;
            border:1px solid #ddd;
            text-decoration:none;
            background:#fff;
            color:#333;
            transition:0.2s;
            font-size:14px;
        }

        .page-link:hover {
            background:#8e44ad;
            color:#fff;
            border-color:#8e44ad;
        }

        .page-item.active .page-link {
            background:#8e44ad;
            color:#fff;
            border-color:#8e44ad;
            font-weight:600;
        }

        .page-item.disabled .page-link {
            opacity:0.5;
            pointer-events:none;
        }

        @media(max-width:600px){
            .page-link{
                padding:6px 10px;
                font-size:12px;
            }
        }
        `;

            document.head.appendChild(style);
        },

        /* ================= URL ================= */
        getUrlPage() {
            const url = new URL(window.location);
            return parseInt(url.searchParams.get("page")) || 1;
        },

        setUrl(page) {
            const url = new URL(window.location);
            url.searchParams.set("page", page);
            window.history.pushState({}, "", url);
        },

        /* ================= AUTO CONTAINER ================= */
        ensureContainer(id) {
            let el = document.getElementById(id);

            if (!el) {
                const wrapper = document.createElement("div");
                wrapper.className = "ds-pagination-wrapper";
                wrapper.id = id + "-wrapper";

                el = document.createElement("ul");
                el.id = id;
                el.className = "pagination";

                wrapper.appendChild(el);
                document.body.appendChild(wrapper);
            }

            return el;
        },

        /* ================= MAIN RENDER ================= */
        render(containerId, meta, onPageChange) {
            this.injectStyle();

            const el = this.ensureContainer(containerId);

            const current = Number(meta.current_page || this.getUrlPage());
            const total = Number(meta.last_page || 1);

            el.innerHTML = "";

            const create = (text, page, disabled = false, active = false) => {
                const li = document.createElement("li");
                li.className = "page-item";

                if (disabled) li.classList.add("disabled");
                if (active) li.classList.add("active");

                const a = document.createElement("a");
                a.className = "page-link";
                a.href = "#";
                a.innerText = text;

                if (!disabled && !active) {
                    a.onclick = e => {
                        e.preventDefault();
                        this.setUrl(page);
                        onPageChange(page);
                    };
                }

                li.appendChild(a);
                return li;
            };

            /* ================= SMART PAGINATION ================= */
            const pages = [];
            const delta = 1;

            // first page
            pages.push(1);

            // middle range
            for (let i = current - delta; i <= current + delta; i++) {
                if (i > 1 && i < total) {
                    pages.push(i);
                }
            }

            // last page
            if (total > 1) pages.push(total);

            // remove duplicates + sort
            const uniquePages = [...new Set(pages)].sort((a, b) => a - b);

            let last = 0;

            // Prev
            el.appendChild(create("Prev", current - 1, current === 1));

            // Pages with ellipsis
            uniquePages.forEach(page => {
                if (last && page - last > 1) {
                    const dots = document.createElement("li");
                    dots.className = "page-item disabled";
                    dots.innerHTML = `<span class="page-link">...</span>`;
                    el.appendChild(dots);
                }

                el.appendChild(create(page, page, false, page === current));

                last = page;
            });

            // Next
            el.appendChild(create("Next", current + 1, current === total));
        }
    };
})(window);
