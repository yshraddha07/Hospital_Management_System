document.addEventListener("DOMContentLoaded", function () {
    const treeContainer = document.getElementById("bst-tree");
    if (!treeContainer) return;

    class BSTNode {
        constructor(type, label, detail, extra, id = null, name = null) {
            this.type = type;
            this.label = label;
            this.detail = detail;
            this.extra = extra;
            this.id = id;
            this.name = name;
            this.left = null;
            this.right = null;
        }
    }

    function patientIdValue(patientId) {
        const raw = String(patientId ?? "");
        const cleaned = raw.replace(/[^0-9.-]/g, "");
        const numeric = Number(cleaned);
        return Number.isFinite(numeric) ? numeric : 0;
    }

    function insertPatient(root, patient) {
        const patientId = patientIdValue(patient.id);
        if (!root) {
            return new BSTNode("patient", "P" + patientId, patient.name, "Patient", patientId, patient.name);
        }

        if (patientId < patientIdValue(root.id)) {
            root.left = insertPatient(root.left, patient);
        } else {
            root.right = insertPatient(root.right, patient);
        }

        return root;
    }

    function buildPatientBST(patients) {
        let root = null;
        (patients || []).forEach(function (patient) {
            if (!patient || patient.id === null || patient.id === undefined) {
                return;
            }
            root = insertPatient(root, patient);
        });
        return root;
    }

    function buildHospitalTree(departments) {
        const hospital = new BSTNode("hospital", "Hospital", "Main Campus", "Root");
        if (departments.length === 2) {
            const leftDepartment = departments[0];
            const rightDepartment = departments[1];

            const leftDoctor = new BSTNode("doctor", leftDepartment.hod, "HOD", "Doctor");
            const rightDoctor = new BSTNode("doctor", rightDepartment.hod, "HOD", "Doctor");
            const leftPatientRoot = buildPatientBST(leftDepartment.patients || []);
            const rightPatientRoot = buildPatientBST(rightDepartment.patients || []);

            const leftDept = new BSTNode("department", leftDepartment.name, "Department", "Branch");
            const rightDept = new BSTNode("department", rightDepartment.name, "Department", "Branch");

            leftDept.left = leftDoctor;
            leftDept.right = leftPatientRoot;
            rightDept.left = rightDoctor;
            rightDept.right = rightPatientRoot;

            hospital.left = leftDept;
            hospital.right = rightDept;
        }

        return hospital;
    }

    function nodeKey(node) {
        if (!node) return "";
        if (node.type === "patient") return "patient-" + node.id;
        if (node.type === "doctor") return "doctor-" + encodeURIComponent(node.label);
        if (node.type === "department") return "department-" + encodeURIComponent(node.label);
        return "hospital";
    }

    function findDepth(node) {
        if (!node) return 0;
        return 1 + Math.max(findDepth(node.left), findDepth(node.right));
    }

    function renderTreeSvg(root) {
        if (!root) return "";

        const width = 1600;
        const height = 720;
        const xStep = 210;
        const yStep = 110;
        const positions = {};

        function assignCoordinates(node, depth, minX, maxX) {
            if (!node) return;

            const x = (minX + maxX) / 2;
            const y = 60 + depth * yStep;
            positions[nodeKey(node)] = { x, y };

            if (node.left) {
                assignCoordinates(node.left, depth + 1, minX, x);
            }
            if (node.right) {
                assignCoordinates(node.right, depth + 1, x, maxX);
            }
        }

        assignCoordinates(root, 0, 50, width - 50);

        const linkPaths = [];
        const nodeEls = [];

        function addNode(node) {
            if (!node) return;

            const pos = positions[nodeKey(node)];
            const key = nodeKey(node);

            let boxClass = "bst-node-box bst-patient";
            let title = "P" + node.id;
            let label = node.name;

            if (node.type === "hospital") {
                boxClass = "bst-node-box bst-hospital";
                title = "Hospital";
                label = "";
            } else if (node.type === "department") {
                boxClass = "bst-node-box bst-department";
                title = "DEPARTMENT";
                label = node.label;
            } else if (node.type === "doctor") {
                boxClass = "bst-node-box bst-doctor";
                title = "HOD";
                label = node.label;
            }

            const nodeHtml = '<g class="bst-node-group" data-node-key="' + key + '" transform="translate(' + (pos.x - 70) + ',' + (pos.y - 28) + ')">' +
                '<rect class="' + boxClass + '" x="0" y="0" width="140" height="56" rx="14" ry="14"></rect>' +
                '<text x="70" y="20" text-anchor="middle" class="bst-node-title">' + title + '</text>' +
                '<text x="70" y="38" text-anchor="middle" class="bst-node-label">' + label + '</text>' +
                '</g>';

            nodeEls.push(nodeHtml);

            if (node.left) {
                const leftPos = positions[nodeKey(node.left)];
                const startX = pos.x;
                const startY = pos.y + 28;
                const endX = leftPos.x;
                const endY = leftPos.y - 28;
                linkPaths.push('<path class="bst-link bst-link-left" d="M ' + startX + ' ' + startY + ' L ' + endX + ' ' + endY + '" />');
                addNode(node.left);
            }

            if (node.right) {
                const rightPos = positions[nodeKey(node.right)];
                const startX = pos.x;
                const startY = pos.y + 28;
                const endX = rightPos.x;
                const endY = rightPos.y - 28;
                linkPaths.push('<path class="bst-link bst-link-right" d="M ' + startX + ' ' + startY + ' L ' + endX + ' ' + endY + '" />');
                addNode(node.right);
            }
        }

        addNode(root);

        return '<svg class="bst-svg" viewBox="0 0 ' + width + ' ' + height + '" preserveAspectRatio="xMidYMin meet">' +
            linkPaths.join("") +
            nodeEls.join("") +
            '</svg>';
    }

    function traverse(node, order) {
        const result = [];

        function visit(current) {
            if (!current) return;

            if (order === "preorder") {
                result.push(current);
            }

            if (current.left) visit(current.left);

            if (order === "inorder") {
                result.push(current);
            }

            if (current.right) visit(current.right);

            if (order === "postorder") {
                result.push(current);
            }
        }

        visit(node);
        return result;
    }

    function getNodeText(node) {
        if (!node) return "";
        if (node.type === "hospital") return "Hospital";
        if (node.type === "department") return node.label;
        if (node.type === "doctor") return node.label;
        return "P" + node.id;
    }

    function animateTraversal(order) {
        const root = buildHospitalTree(treeData);
        const nodes = traverse(root, order);
        const result = [];
        const allNodeEls = document.querySelectorAll("[data-node-key]");
        allNodeEls.forEach(function (el) {
            el.classList.remove("bst-visited", "bst-current");
        });

        nodes.forEach(function (node, index) {
            const key = nodeKey(node);
            const target = document.querySelector('[data-node-key="' + key + '"]');
            if (!target) return;

            setTimeout(function () {
                target.classList.remove("bst-current");
                target.classList.add("bst-current");
                result.push(getNodeText(node));

                const currentText = result.join(" → ");
                document.getElementById("traversal-result").textContent = order.charAt(0).toUpperCase() + order.slice(1) + ": " + currentText;
                document.getElementById("traversal-count").textContent = "Total Nodes Visited: " + result.length;

                setTimeout(function () {
                    target.classList.remove("bst-current");
                    target.classList.add("bst-visited");
                }, 300);
            }, index * 500);
        });
    }

    const hospitalTree = buildHospitalTree(treeData);
    treeContainer.innerHTML = renderTreeSvg(hospitalTree);

    document.querySelectorAll(".traversal-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            const order = button.getAttribute("data-order");
            animateTraversal(order);
        });
    });

    if (treeData && treeData.length === 2) {
        const interval = setInterval(function () {
            fetch(window.location.pathname + "?tree_json=1&departments=" + encodeURIComponent(treeContainer.dataset.selected), {
                method: "GET",
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data && data.length === 2) {
                        treeData.length = 0;
                        data.forEach(function (item) { treeData.push(item); });
                        const newRoot = buildHospitalTree(treeData);
                        treeContainer.innerHTML = renderTreeSvg(newRoot);
                    }
                })
                .catch(function () {});
        }, 3000);
    }
});