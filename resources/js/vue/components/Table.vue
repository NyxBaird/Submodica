<style lang="scss" scoped>
@import "../../../sass/frontend/table.scss";
</style>

<style lang="scss">
@import "../../../sass/variables";

.tableHeader {
    select {
        padding: 2px 15px;
        background-color: rgba(255, 255, 255, $formOpacity);
    }
}
</style>

<template>
    <div class="tableHeader">
        <slot name="searchPrefix"></slot>
        <input type="text" placeholder="Search..." v-model="search" @keydown.enter="fetchData">
        <button class="btn btnGreen" @click="fetchData">
            <font-awesome-icon icon="fa-solid fa-search" />
        </button>
    </div>
    <table>
        <thead>
            <tr>
                <th v-for="column in columnData"
                    :class="column.classes"
                    :id="column.selector"
                    :style="column.style"
                    :title="typeof column.title !== 'undefined' ? column.title : ''"
                    @click="toggleSort($event)">

                    <span class="name">
                        {{ column.name }}
                        <font-awesome-icon :icon="column.icon" v-if="!column.name.length && typeof column.icon !== 'undefined'" />
                    </span>

                    <span v-if="typeof column.sortable !== 'undefined' && column.sortable" class="sort">
                        &nbsp;<font-awesome-icon :icon="typeof column.sort === 'undefined' ? 'fa-solid fa-minus' : column.sort.icon" />
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="row in rowData">
                <td v-for="column in columnData" :class="column.classes">
                    <span v-if="typeof column.image !== 'undefined'"
                          :style="{'cursor': typeof column.link !== 'undefined' ? 'pointer' : 'default'}"
                          @click="typeof column.link !== 'undefined'
                                ? sendToUrl(column.link.href.replace(':game', this.$route.params.game).replace(':id', row.id))
                                : ''">

                        <img :width="iconSize"
                             :height="iconSize"
                             :src="row[column.selector]"
                             alt="Icon Image">
                    </span>

                    <span v-else>
                        <a v-if="typeof column.link !== 'undefined'"
                           :href="column.link.href.replace(':game', this.$route.params.game).replace(':id', row.id)" :target="typeof column.link.target !== 'undefined' ? column.link.target : '_self'">
                            {{ typeof column.render === "undefined" ? row[column.selector] : column.render(row) }}
                        </a>
                        <span v-else>
                            {{ typeof column.render === "undefined" ? row[column.selector] : column.render(row) }}
                        </span>

                        <span v-if="typeof column.subtext !== 'undefined'" class="subtext">
                            <span v-if="typeof column.subtext.selector !== 'undefined'">
                                {{ row[column.subtext.selector] }}
                            </span>
                        </span>
                    </span>

                    <div class="buttons" v-if="typeof column.buttons !== 'undefined'">
                        <button v-for="button in column.buttons"
                                class="btn"
                            :class="button.classes"
                            @click="button.callback($event, row)"
                            :title="(typeof button.title !== 'undefined' ? button.title : '')">

                            <font-awesome-icon v-if="typeof button.icon !== 'undefined'" :icon="[button.icon.prefix, button.icon.iconName]" />
                            <span v-if="typeof button.text !== 'undefined'">{{ button.text }}</span>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="tableFooter">
        <span class="results">
            Showing {{ rangeMessage }} of {{ totalResults }} results
        </span>
        <div class="pagination">
            <a href="#"
               @click="turnToPage(page)"
               class="page"
               :class="pages.current === page ? 'active' : ''"
               v-for="page in pages.total">{{ page }}</a>
        </div>
    </div>
</template>

<script>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library } from "@fortawesome/fontawesome-svg-core"
import { faCaretUp, faCaretDown, faMinus, faSearch } from '@fortawesome/free-solid-svg-icons'
library.add(faCaretUp, faCaretDown, faMinus, faSearch);

export default {
    name: "Table",
    components: {
        FontAwesomeIcon
    },
    props: {
        src: String,
        columns: Array,
        data: Array,
        sort: {
            type: Array,
            required: false,
            default: []
        }
    },
    data() {
        return {
            search: "",
            lastSearch: "",
            searchPrefix: "",
            columnData: [
                //Example column
                {
                    name: "Filler Data",
                    icon: {
                        prefix: "fas",
                        iconName: "download"
                    },
                    selector: "filler",
                    sortable: true,
                    searchable: true,
                    classes: "center",
                    style: {
                        width: "100%"
                    }
                }
            ],
            rowData: [
                //Example row
                {
                    example: "example"
                }
            ],
            pages: {
                total: 1,
                resultsPerPage: 10,
                current: 1
            },
            totalResults: 0,
            iconSize: 100
        };
    },
    mounted() {
        this.columnData = this.columns;
        this.rowData = this.data;

        this.columnData.forEach(column => {
            if (typeof column.sortable !== 'undefined' && column.sortable)
                column.style.cursor = 'pointer';
        });

        this.sort.forEach(sort => {
            this.columnData[this.columnData.findIndex(column => column.selector === sort.selector)].sort = {
                icon: sort.direction === 'asc' ? 'fa-solid fa-caret-up' : 'fa-solid fa-caret-down',
                direction: sort.direction
            };
        });

        this.fetchData();
    },
    methods: {
        turnToPage(page) {
            if (page === this.pages.current)
                return;
            else
                this.pages.current = page;

            this.fetchData();
        },
        fetchData(src) {
            if (typeof src !== 'string')
                src = this.src;

            let redirectFilter = {};
            if (typeof window.tableSearch !== 'undefined')
                redirectFilter = window.tableSearch;

            //WIP use our computed tableLoadAttrs to fetch data from the server
            if (this.$parent.$data.searchPrefix !== this.searchPrefix) {
                this.pages.current = 1;
                this.searchPrefix = this.$parent.$data.searchPrefix;
            }

            //reset our pagination if the user searches something
            if (this.search.length && this.search !== this.lastSearch)
                this.pages.current = 1;

            this.lastSearch = this.search;

            //Request our new data and update the table
            this.$axios
                .post(src, {
                    columns: this.columnData,
                    pages: this.pages,
                    search: this.search,
                    searchPrefix: this.searchPrefix,
                    redirectFilter: window.tableSearch
                }).then((response) => {

                    this.rowData = response.data.data.data;
                    this.pages.total = Math.ceil(response.data.data.filters.pages.total);
                    this.totalResults = response.data.data.filters.totalResults;
                });
        },
        toggleSort($event) {

            //remove sort from all columns
            for (let c in this.columnData)
                if (typeof this.columnData[c].sort !== 'undefined' && $event.currentTarget.id !== this.columnData[c].selector)
                    delete this.columnData[c].sort;

            let sort = this.columnData[this.columnData.findIndex(column => column.selector === $event.currentTarget.id)].sort;
            if (typeof sort === 'undefined' || !sort.direction) {
                sort = {
                    icon: 'fa-solid fa-caret-down',
                    direction: 'desc'
                };
            } else if (sort.direction === 'desc') {
                sort = {
                    icon: 'fa-solid fa-caret-up',
                    direction: 'asc'
                };
            } else {
                sort = {
                    icon: 'fa-solid fa-minus',
                    direction: null
                };
            }

            this.columnData[this.columnData.findIndex(column => column.selector === $event.currentTarget.id)].sort = sort;
            this.fetchData();
        },
        sendToUrl(url) {
            window.open(url, '_blank');
        }
    },
    computed: {
        rangeMessage() {
            let start = (this.pages.current - 1) * this.pages.resultsPerPage + 1;
            let end = Math.min(this.pages.current * this.pages.resultsPerPage, this.totalResults);
            return `${start}-${end}`;
        }
    }
}
</script>
