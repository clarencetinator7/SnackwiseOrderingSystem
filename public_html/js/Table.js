"use strict";
let rowCounter;
let currentRowCount = 0;
let clicked;
let verifyUpdate;


function dataAdded() {
    rowCounter = true;
    clicked = "added";
    verifyUpdate = true;
}

function dataRemoved(){
   
    rowCounter = true;
    clicked = "removed";
    verifyUpdate = true;
}


function _toConsumableArray(e) {
    return _arrayWithoutHoles(e) || _iterableToArray(e) || _unsupportedIterableToArray(e) || _nonIterableSpread()
}

function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
}

function _unsupportedIterableToArray(e, t) {
    if (e) {
        if ("string" == typeof e) return _arrayLikeToArray(e, t);
        var a = Object.prototype.toString.call(e).slice(8, -1);
        return "Object" === a && e.constructor && (a = e.constructor.name), "Map" === a || "Set" === a ? Array.from(e) : "Arguments" === a || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a) ? _arrayLikeToArray(e, t) : void 0
    }
}

function _iterableToArray(e) {
    if ("undefined" != typeof Symbol && Symbol.iterator in Object(e)) return Array.from(e)
}

function _arrayWithoutHoles(e) {
    if (Array.isArray(e)) return _arrayLikeToArray(e)
}

function _arrayLikeToArray(e, t) {
    (null == t || t > e.length) && (t = e.length);
    for (var a = 0, s = new Array(t); a < t; a++) s[a] = e[a];
    return s
}

function _classCallCheck(e, t) {
    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
}

function _defineProperties(e, t) {
    for (var a = 0; a < t.length; a++) {
        var s = t[a];
        s.enumerable = s.enumerable || !1, s.configurable = !0, "value" in s && (s.writable = !0), Object.defineProperty(e, s.key, s)
    }
}

function _createClass(e, t, a) {
    return t && _defineProperties(e.prototype, t), a && _defineProperties(e, a), e
}
/*!
 * JSTable
 */
var JSTableDefaultConfig = {
        perPage: 5,
        perPageSelect: [5, 10, 15, 20, 25],
        sortable: !0,
        searchable: !0,
        nextPrev: !0,
        firstLast: !1,
        prevText: "&lsaquo;",
        nextText: "&rsaquo;",
        firstText: "&laquo;",
        lastText: "&raquo;",
        ellipsisText: "&hellip;",
        truncatePager: !0,
        pagerDelta: 2,
        classes: {
            top: "dt-top",
            info: "dt-info",
            input: "dt-input",
            table: "dt-table",
            bottom: "dt-bottom",
            search: "dt-search",
            sorter: "dt-sorter",
            wrapper: "dt-wrapper",
            dropdown: "dt-dropdown",
            ellipsis: "dt-ellipsis",
            selector: "dt-selector",
            container: "dt-container",
            pagination: "dt-pagination",
            loading: "dt-loading",
            message: "dt-message"
        },
        labels: {
            placeholder: "Search",
            perPage: "{select} ",
            /* perPage: "{select} entries per page", */
            noRows: "No entries found",
            info: "Showing {start} to {end} of {rows} entries",
            loading: "Loading...",
            infoFiltered: "Showing {start} to {end} of {rows} entries (filtered from {rowsTotal} entries)"
        },
        layout: {
            top: "{select}{search}",
            bottom: "{info}{pager}"
        },
        serverSide: !1,
        deferLoading: null,
        ajax: null,
        ajaxParams: {}
    },
    JSTable = function () {
        function e(t) {
            var a = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {};
            _classCallCheck(this, e);
            var s = t;
            "string" == typeof t && (s = document.querySelector(t)), null !== s && (this.config = Object.assign({}, JSTableDefaultConfig, a), this.table = new JSTableElement(s), this.currentPage = 1, this.columnRenderers = [], this.columnsNotSearchable = [], this.searchQuery = null, this.sortColumn = null, this.sortDirection = "asc", this.isSearching = !1, this.filteredDataCount = null, this.pager = new JSTablePager(this), this._build(), this._buildColumns(), this.update(!this.config.serverSide), this._bindEvents(), this._emit("init"))
        }
        return _createClass(e, [{
            key: "_build",
            value: function () {
                var e = this.config;
                this.wrapper = document.createElement("div"), this.wrapper.className = e.classes.wrapper;
                var t = ["<div class='", e.classes.top, "'>", e.layout.top, "</div>", "<div class='", e.classes.container, "'>", "<div class='", e.classes.loading, " hidden'>", e.labels.loading, "</div>", "</div>", "<div class='", e.classes.bottom, "'>", e.layout.bottom, "</div>"].join("");
                if (t = t.replace("{info}", "<div class='" + e.classes.info + "'></div>"), e.perPageSelect) {
                    var a = ["<div class='", e.classes.dropdown, "'>", "<label>", e.labels.perPage, "</label>", "</div>"].join(""),
                        s = document.createElement("select");
                    s.className = e.classes.selector, e.perPageSelect.forEach((function (t) {
                        var a = t === e.perPage,
                            r = new Option(t, t, a, a);
                        s.add(r)
                    })), a = a.replace("{select}", s.outerHTML), t = t.replace(/\{select\}/g, a)
                } else t = t.replace(/\{select\}/g, "");
                if (e.searchable) {
                    var r = ["<div class='", e.classes.search, "'>", "<input class='", e.classes.input, "' placeholder='", e.labels.placeholder, "' type='text'>", "</div>"].join("");
                    t = t.replace(/\{search\}/g, r)
                } else t = t.replace(/\{search\}/g, "");
                this.table.element.classList.add(e.classes.table), t = t.replace("{pager}", "<div class='" + e.classes.pagination + "'></div>"), this.wrapper.innerHTML = t, this.table.element.parentNode.replaceChild(this.wrapper, this.table.element), this.wrapper.querySelector("." + e.classes.container).appendChild(this.table.element), this._updatePagination(), this._updateInfo()
            }
        }, {
            key: "update",
            value: function () {
                var e = !(arguments.length > 0 && void 0 !== arguments[0]) || arguments[0],
                    t = this;
                this.currentPage > this.pager.getPages() && (this.currentPage = this.pager.getPages());
                var a = t.wrapper.querySelector(" ." + t.config.classes.loading);
                a.classList.remove("hidden"), this.table.header.getCells().forEach((function (e, a) {
                    var s = t.table.head.rows[0].cells[a];
                    for (var r in s.innerHTML = e.getContent(), e.classes.length > 0 && (s.className = e.classes.join(" ")), e.attributes) s.setAttribute(r, e.attributes[r]);
                    s.setAttribute("data-sortable", e.isSortable)
                })), e ? this.getPageData(this.currentPage).then((function (e) {
                    t.table.element.classList.remove("hidden"), t.table.body.innerHTML = "", e.forEach((function (e) {
                        t.table.body.appendChild(e.getFormated(t.columnRenderers))
                    })), a.classList.add("hidden")
                })).then((function () {
                    t.getDataCount() <= 0 && (t.wrapper.classList.remove("search-results"), t.setMessage(t.config.labels.noRows)), t._emit("update"), t._updateInfo()
                })).then((function () {
                    t._updatePagination(), t._updateInfo()
                })) : (t.table.element.classList.remove("hidden"), t.table.body.innerHTML = "", this.getDataCount() <= 0 && (t.wrapper.classList.remove("search-results"), t.setMessage(t.config.labels.noRows)), this._getData().forEach((function (e) {
                    t.table.body.appendChild(e.getFormated(t.columnRenderers))
                })), a.classList.add("hidden"))

                /* this._updateInfo(); */
            }
        }, {
            key: "_updatePagination",
            value: function () {
                var e = this.wrapper.querySelector(" ." + this.config.classes.pagination);
                e.innerHTML = "", e.appendChild(this.pager.render(this.currentPage))
            }
        }, {
            key: "_updateInfo",
            value: function () {
                var e = this.wrapper.querySelector(" ." + this.config.classes.info),
                    t = this.isSearching ? this.config.labels.infoFiltered : this.config.labels.info;
                if (e && t.length) {
                    var a = t.replace("{start}", currentRowCount > 0 ? this._getPageStartIndex() + 1 : 0).replace("{end}", this.updateDataCountTotal()).replace("{page}", this.currentPage).replace("{pages}", this.pager.getPages()).replace("{rows}", this.updateDataCountTotal()).replace("{rowsTotal}", this.updateDataCountTotal());
                    e.innerHTML = a
                }
            }
        }, {
            key: "_getPageStartIndex",
            value: function () {
                return (this.currentPage - 1) * this.config.perPage
            }
        }, {
            key: "_getPageEndIndex",
            
            value: function () {
                var e = this.currentPage * this.config.perPage - 1;
                return e > this.getDataCount() - 1 ? this.getDataCount() - 1 : e
            }
        }, {
            key: "_getData",
     
            value: function () {
                return this._emit("getData", this.table.dataRows), this.table.dataRows.filter((function (e) {
                    
                    return e.visible
                }))
            }
        }, {
            key: "_fetchData",
            value: function () {
                var e = this,
                    t = {
                        searchQuery: this.searchQuery,
                        sortColumn: this.sortColumn,
                        sortDirection: this.sortDirection,
                        start: this._getPageStartIndex(),
                        length: this.config.perPage,
                        datatable: 1
                    };
                t = Object.assign({}, this.config.ajaxParams, t);
                var a = this.config.ajax + "?" + this._queryParams(t);
                return fetch(a, {
                    method: "GET",
                    credentials: "same-origin",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json"
                    }
                }).then((function (e) {
                    return e.json()
                })).then((function (t) {
                    return e._emit("fetchData", t), e.filteredDataCount = t.recordsFiltered, t.data
                })).then((function (e) {
                    var t = [];
                    return e.forEach((function (e) {
                        t.push(JSTableRow.createFromData(e))
                    })), t
                })).catch((function (e) {
                    console.error(e)
                }))
            }
        }, {
            key: "_queryParams",
            value: function (e) {
                return Object.keys(e).map((function (t) {
                    return encodeURIComponent(t) + "=" + encodeURIComponent(e[t])
                })).join("&")
            }
        }, {
            key: "getDataCount",
            value: function () {
                return this.isSearching ? this.getDataCountFiltered() : this.getDataCountTotal()
            }
        }, {
            key: "getDataCountFiltered",
            value: function () {
                return this.config.serverSide ? this.filteredDataCount : this._getData().length
            }
        }, {
            key: "getDataCountTotal",
            value: function () {
             

                    return this.config.serverSide ? this.config.deferLoading : this.table.dataRows.length;
                
            }
        } ,

        {
            key: "updateDataCountTotal",
            value: function () {
                if(rowCounter) {

                    if (verifyUpdate) {
                    if(clicked == "added") {
                        currentRowCount = currentRowCount+1;
                    } else if(clicked == "removed"){
                        currentRowCount = currentRowCount-1;
                    }
                    verifyUpdate=false;
                }
                  
                    return this.config.serverSide ? currentRowCount : this.table.dataRows.length;
                } else {
                  
                    currentRowCount = this.config.deferLoading;
                    return this.config.serverSide ? currentRowCount : this.table.dataRows.length;
                }
            }
        },
        
        
        {
            key: "getPageData",
            value: function () {
                if (this.config.serverSide) return this._fetchData();
                var e = this._getPageStartIndex(),
                    t = this.updateDataCountTotal();
                return Promise.resolve(this._getData()).then((function (a) {
                    return a.filter((function (a, s) {
                        return s >= e && s <= t
                    }))
                }))
            }
        }, {
            key: "search",
            value: function (e) {
                var t = this;
                if (this.searchQuery = e.toLowerCase(), this.currentPage = 1, this.isSearching = !0, !this.searchQuery.length) return this.table.dataRows.forEach((function (e) {
                    e.visible = !0
                })), this.isSearching = !1, t.wrapper.classList.remove("search-results"), t.update(), !1;
                this.config.serverSide || this.table.dataRows.forEach((function (e) {
                    e.visible = !1, t.searchQuery.split(" ").reduce((function (a, s) {
                        var r, n = e.getCells();
                        return r = (n = n.filter((function (e, a) {
                            if (t.columnsNotSearchable.indexOf(a) < 0) return !0
                        }))).some((function (e, t) {
                            if (e.getContent().toLowerCase().indexOf(s) >= 0) return !0
                        })), a && r
                    }), !0) && (e.visible = !0)
                })), this.wrapper.classList.add("search-results"), this.update(), this._emit("search", e)
            }
        }, {
            key: "sort",
            value: function (e, t) {
                var a = arguments.length > 2 && void 0 !== arguments[2] && arguments[2],
                    s = this;
                if (this.sortColumn = e || 0, this.sortDirection = t, this.sortColumn < 0 || this.sortColumn > this.table.getColumnCount() - 1) return !1;
                var r = this.table.header.getCell(this.sortColumn),
                    n = this.table.dataRows,
                    i = this.table.header.getCells();
                i.forEach((function (e) {
                    e.removeClass("asc"), e.removeClass("desc")
                })), r.addClass(this.sortDirection), this.config.serverSide || (n = n.sort((function (e, t) {
                    var a = e.getCellContent(s.sortColumn).toLowerCase(),
                        r = t.getCellContent(s.sortColumn).toLowerCase();
                    return a = a.replace(/(\$|\,|\s|%)/g, ""), r = r.replace(/(\$|\,|\s|%)/g, ""), a = isNaN(a) || "" === a ? a : parseFloat(a), r = isNaN(r) || "" === r ? r : parseFloat(r), "" === a && "" !== r ? "asc" === s.sortDirection ? 1 : -1 : "" !== a && "" === r ? "asc" === s.sortDirection ? -1 : 1 : "asc" === s.sortDirection ? a === r ? 0 : a > r ? 1 : -1 : a === r ? 0 : a < r ? 1 : -1
                })), this.table.dataRows = n), this.config.serverSide && a || this.update(), this._emit("sort", this.sortColumn, this.sortDirection)
            }
        }, {
            key: "_bindEvents",
            value: function () {
                var e = this;
                this.wrapper.addEventListener("click", (function (t) {
                    var a = t.target;
                    if (a.hasAttribute("data-page")) {
                        t.preventDefault();
                        var s = parseInt(a.getAttribute("data-page"), 10);
                        e._emit("paginate", e.currentPage, s), e.currentPage = s, e.update()
                    }
                    if ("TH" === a.nodeName && a.hasAttribute("data-sortable")) {
                        if ("false" === a.getAttribute("data-sortable")) return !1;
                        t.preventDefault(), e.sort(a.cellIndex, a.classList.contains("asc") ? "desc" : "asc")
                    }
                })), this.config.perPageSelect && this.wrapper.addEventListener("change", (function (t) {
                    var a = t.target;
                    if ("SELECT" === a.nodeName && a.classList.contains(e.config.classes.selector)) {
                        t.preventDefault();
                        var s = parseInt(a.value, 10);
                        e._emit("perPageChange", e.config.perPage, s), e.config.perPage = s, e.update()
                    }
                })), this.config.searchable && this.wrapper.addEventListener("keyup", (function (t) {
                    "INPUT" === t.target.nodeName && t.target.classList.contains(e.config.classes.input) && (t.preventDefault(), e.search(t.target.value))
                }))
            }
        }, {
            key: "on",
            value: function (e, t) {
                this.events = this.events || {}, this.events[e] = this.events[e] || [], this.events[e].push(t)
            }
        }, {
            key: "off",
            value: function (e, t) {
                this.events = this.events || {}, e in this.events != !1 && this.events[e].splice(this.events[e].indexOf(t), 1)
            }
        }, {
            key: "_emit",
            value: function (e) {
                if (this.events = this.events || {}, e in this.events != !1)
                    for (var t = 0; t < this.events[e].length; t++) this.events[e][t].apply(this, Array.prototype.slice.call(arguments, 1))
            }
        }, {
            key: "setMessage",
            value: function (e) {
                var t = this.table.getColumnCount(),
                    a = document.createElement("tr");
                a.innerHTML = '<td class="' + this.config.classes.message + '" colspan="' + t + '">' + e + "</td>", this.table.body.innerHTML = "", this.table.body.appendChild(a)
            }
        }, {
            key: "_buildColumns",
            value: function () {
                var e = this,
                    t = null,
                    a = null;
                this.config.columns && this.config.columns.forEach((function (s) {
                    isNaN(s.select) || (s.select = [s.select]), s.select.forEach((function (r) {
                        var n = e.table.header.getCell(r);
                        if (s.hasOwnProperty("render") && "function" == typeof s.render && (e.columnRenderers[r] = s.render), s.hasOwnProperty("sortable")) {
                            var i = !1;
                            n.hasSortable ? i = n.isSortable : (i = s.sortable, n.setSortable(i)), i && (n.addClass(e.config.classes.sorter), s.hasOwnProperty("sort") && 1 === s.select.length && (t = s.select[0], a = s.sort))
                        }
                        s.hasOwnProperty("searchable") && (n.setAttribute("data-searchable", s.searchable), !1 === s.searchable && e.columnsNotSearchable.push(r))
                    }))
                })), this.table.header.getCells().forEach((function (s, r) {
                    var n = !1;
                    s.hasSortable ? n = s.isSortable : (n = e.config.sortable, s.setSortable(n)), n && (s.addClass(e.config.classes.sorter), s.hasSort && (t = r, a = s.sortDirection))
                })), null !== t && e.sort(t, a, !0)
            }
        }]), e
    }(),

    JSTableElement = function () {
        function e(t) {
            _classCallCheck(this, e), this.element = t, this.body = this.element.tBodies[0], this.head = this.element.tHead, this.rows = Array.from(this.element.rows).map((function (e) {
                return new JSTableRow(e, e.parentNode.nodeName)
            })), this.dataRows = this._getBodyRows(), this.header = this._getHeaderRow()
        }
        return _createClass(e, [{
            key: "_getBodyRows",
            value: function () {
                return this.rows.filter((function (e) {
                    return !e.isHeader && !e.isFooter
                }))
            }
        }, {
            key: "_getHeaderRow",
            value: function () {
                return this.rows.find((function (e) {
                    return e.isHeader
                }))
            }
        }, {
            key: "getColumnCount",
            value: function () {
                return this.header.getColumnCount()
            }
        }, {
            key: "getFooterRow",
            value: function () {
                return this.rows.find((function (e) {
                    return e.isFooter
                }))
            }
        }]), e
    }(),
    JSTableRow = function () {
        function e(t, a) {
            _classCallCheck(this, e), this.cells = Array.from(t.cells).map((function (e) {
                return new JSTableCell(e)
            })), this.d = this.cells.length, this.isHeader = "THEAD" === a, this.isFooter = "TFOOT" === a, this.visible = !0
        }
        return _createClass(e, [{
            key: "getCells",
            value: function () {
                return Array.from(this.cells)
            }
        }, {
            key: "getColumnCount",
            value: function () {
                return this.cells.length
            }
        }, {
            key: "getCell",
            value: function (e) {
                return this.cells[e]
            }
        }, {
            key: "getCellContent",
            value: function (e) {
                return this.getCell(e).getContent()
            }
        }, {
            key: "getFormated",
            value: function (e) {
                var t = document.createElement("tr"),
                    a = this;
                return this.getCells().forEach((function (s, r) {
                    var n = document.createElement("td");
                    for (var i in n.innerHTML = s.getContent(), e.hasOwnProperty(r) && (n.innerHTML = e[r].call(a, s.getElement(), r)), s.classes.length > 0 && (n.className = s.classes.join(" ")), s.attributes) n.setAttribute(i, s.attributes[i]);
                    t.appendChild(n)
                })), t
            }
        }, {
            key: "setCellContent",
            value: function (e, t) {
                this.cells[e].setContent(t)
            }
        }, {
            key: "setCellClass",
            value: function (e, t) {
                this.cells[e].setClassName(t)
            }
        }], [{
            key: "createFromData",
            value: function (t) {
                var a = document.createElement("tr");
                return t.forEach((function (e) {
                    var t = document.createElement("td");
                    t.innerHTML = e, a.appendChild(t)
                })), new e(a)
            }
        }]), e
    }(),
    JSTableCell = function () {
        function e(t) {
            _classCallCheck(this, e), this.content = t.innerHTML, this.className = "", this.element = t, this.hasSortable = t.hasAttribute("data-sortable"), this.isSortable = "true" === t.getAttribute("data-sortable"), this.hasSort = t.hasAttribute("data-sort"), this.sortDirection = t.getAttribute("data-sort"), this.classes = [];
            var a = this;
            this.attributes = {}, _toConsumableArray(t.attributes).forEach((function (e) {
                a.attributes[e.name] = e.value
            }))
        }
        return _createClass(e, [{
            key: "getElement",
            value: function () {
                return this.element
            }
        }, {
            key: "getContent",
            value: function () {
                return this.content
            }
        }, {
            key: "setContent",
            value: function (e) {
                this.content = e
            }
        }, {
            key: "setClass",
            value: function (e) {
                this.className = e
            }
        }, {
            key: "setSortable",
            value: function (e) {
                this.isSortable = e
            }
        }, {
            key: "addClass",
            value: function (e) {
                this.classes.push(e)
            }
        }, {
            key: "removeClass",
            value: function (e) {
                this.classes.indexOf(e) >= 0 && this.classes.splice(this.classes.indexOf(e), 1)
            }
        }]), e
    }(),
    JSTablePager = function () {
        function e(t) {
            _classCallCheck(this, e), this.instance = t
        }
        return _createClass(e, [{
            key: "getPages",
            value: function () {
                var e = Math.ceil(this.instance.updateDataCountTotal() / this.instance.config.perPage);
                return 0 === e ? 1 : e
            }
        }, {
            key: "render",
            value: function () {
                var e = this.instance.config,
                    t = this.getPages(),
                    a = document.createElement("ul");
                if (t > 1) {
                    var s = 1 === this.instance.currentPage ? 1 : this.instance.currentPage - 1,
                        r = this.instance.currentPage === t ? t : this.instance.currentPage + 1;
                    e.firstLast && a.appendChild(this.createItem("pager", 1, e.firstText)), e.nextPrev && a.appendChild(this.createItem("pager", s, e.prevText)), this.truncate().forEach((function (e) {
                        a.appendChild(e)
                    })), e.nextPrev && a.appendChild(this.createItem("pager", r, e.nextText)), e.firstLast && a.appendChild(this.createItem("pager", t, e.lastText))
                }
                return a
            }
        }, {
            key: "createItem",
            value: function (e, t, a, s) {
                var r = document.createElement("li");
                return r.className = e, r.innerHTML = s ? "<span>" + a + "</span>" : '<a href="#" data-page="' + t + '">' + a + "</a>", r
            }
        }, {
            key: "isValidPage",
            value: function (e) {
                return e > 0 && e <= this.getPages()
            }
        }, {
            key: "truncate",
            value: function () {
                var e, t = this,
                    a = t.instance.config,
                    s = 2 * a.pagerDelta,
                    r = t.instance.currentPage,
                    n = r - a.pagerDelta,
                    i = r + a.pagerDelta,
                    o = this.getPages(),
                    l = [],
                    c = [];
                if (this.instance.config.truncatePager) {
                    r < 4 - a.pagerDelta + s ? i = 3 + s : r > this.getPages() - (3 - a.pagerDelta + s) && (n = this.getPages() - (2 + s));
                    for (var u = 1; u <= o; u++)(1 === u || u === o || u >= n && u <= i) && l.push(u);
                    l.forEach((function (s) {
                        e && (s - e == 2 ? c.push(t.createItem("", e + 1, e + 1)) : s - e != 1 && c.push(t.createItem(a.classes.ellipsis, 0, a.ellipsisText, !0))), c.push(t.createItem(s == r ? "active" : "", s, s)), e = s
                    }))
                } else
                    for (var h = 1; h <= this.getPages(); h++) c.push(this.createItem(h === r ? "active" : "", h, h));
                return c
            }
        }]), e
    }();