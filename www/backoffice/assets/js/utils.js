jQuery.fn.serializeObject = function() {
    var arrayData, objectData;
    arrayData = this.serializeArray();
    objectData = {};

    $.each(arrayData, function() {
        var value;

        if (this.value != null) {
            value = this.value;
        } else {
            value = '';
        }

        if (objectData[this.name] != null) {
            if (!objectData[this.name].push) {
                objectData[this.name] = [objectData[this.name]];
            }

            objectData[this.name].push(value);
        } else {
            objectData[this.name] = value;
        }
    });

    return objectData;
};

var OOC = {
    getCookie: function(name){
        function escape(s) { return s.replace(/([.*+?\^${}()|\[\]\/\\])/g, '\\$1'); };
        var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
        return match ? match[1] : null;
    },
    registerEnterKey: function(formEl, callback){
        formEl.keypress(function(evt){
            var keyCode = evt.which;
            if (keyCode === 13) {
                evt.preventDefault();
                callback();
            }
        });
    },
    registerEscKey: function(formEl, callback) {
        formEl.keydown(function (evt) {
            var keyCode = evt.which;
            if (keyCode === 27) {
                evt.preventDefault();
                callback();
            }
        });
    },
    getOsmLayer: function(){
        /*var url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        var attribution = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';*/
        var url = 'https://europa.eu/webtools/maps/tiles/osm-ec/{z}/{x}/{y}.png';
        var attribution = 'Credit: <a href="http://ec.europa.eu/eurostat/web/gisco/overview" target="_blank">EC-GISCO</a>, © EuroGeographics for the administrative boundaries, © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors | ' +
            '<a href="" class="ec-disclaimer" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" ' +
            'data-content="The designations employed and the presentation of material on this map do not imply the expression of any opinion whatsoever on the part of the European Union concerning the legal status of any country, territory, city or area or of its authorities, or concerning the delimitation of its frontiers or boundaries. Kosovo*: This designation is without prejudice to positions on status, and is in line with UNSCR 1244/1999 and the ICJ Opinion on the Kosovo declaration of independence. Palestine*: This designation shall not be construed as recognition of a State of Palestine and is without prejudice to the individual positions of the Member States on this issue.">Disclaimer</a>';


        return new L.TileLayer(url, {
            minZoom: 1,
            maxZoom: 18,
            attribution: attribution,
            accessToken: 'pk.eyJ1IjoibWZhbGxpc2UiLCJhIjoiY2pwc2dmd2x6MTNydjQzbjltZnUybXhtaiJ9.akD0UR5u-6iJdVOpNG9WGQ'
        });
    },
    capitalizeAddress: function (address) {
        var srcStr = address.split(' ');
        var str = srcStr.filter(function(value, index, arr){
            return value !== '';
        });

        for (var i = 0, x = str.length; i < x; i++) {
            str[i] = str[i][0].toUpperCase() + str[i].substr(1);
        }

        return str.join(" ");
    },
    getFromLocalStorage: function(){
        var data = JSON.parse(localStorage.getItem('ooc-dashboard'));

        if (data !== null){
            if (data.tab === '#users') {
                return this.processFromStorageToUsers(data);
            } else if (data.tab === '#organisations'){
                return this.processFromStorageToOrganisations(data);
            } else if (data.tab === '#organisations') {
                return this.processFromStorageToCommits(data);
            } else {
                return this.processFromStorageToOverview(data);
            }
        }
    },
    setSubmittedMsg: function(){
        localStorage.setItem('ooc-submitted', true);
    },
    getSubmittedMsg: function(){
        return JSON.parse(localStorage.getItem('ooc-submitted'));
    },
    removeSubmittedMsg: function(){
        localStorage.removeItem('ooc-submitted');
    },
    setUserSectionState: function(delState, rejState){
        localStorage.setItem('ooc-deleted-user', delState);
        localStorage.setItem('ooc-rejected-user', rejState);
    },
    getUserSectionState: function(){
        return [localStorage.getItem('ooc-deleted-user'),localStorage.getItem('ooc-rejected-user')]
    },
    removeUserSectionState: function(){
        localStorage.removeItem('ooc-deleted-user');
        localStorage.removeItem('ooc-rejected-user');
    },
    setToLocalSorage: function(data, tab){
        var final = {
            tab: tab,
            filters: data
        };

        localStorage.setItem('ooc-dashboard', JSON.stringify(final));
    },
    setSettingsToLocalStorage: function(tab){
        localStorage.setItem('ooc-settings', tab);
    },
    getSettingsFromLocalStorage: function(){
        return localStorage.getItem('ooc-settings');
    },
    removeFromLocalStorage: function(key){
        localStorage.removeItem(key);
    },
    removeDataTablesStorage: function(namedKey){
        if (namedKey === 'all'){
            namedKey = 'DataTables';
        }

        for (var key in localStorage){
            if (key.indexOf(namedKey) !== -1){
                this.removeFromLocalStorage(key);
            }
        }
    },
    cleanDataTablesStorage: function(key){
        var keys = ['users-table', 'commits-table', 'org-table'];
        var idx = keys.indexOf(key);
        if (idx !== -1){
            keys.splice(idx, 1);
        }
        this.removeDataTablesStorage(keys);
    },
    processFromStorageToOverview: function(data){
        var final   = {
            tab: data.tab
        };

        return final;
    },
    processFromStorageToCommits: function(data){
        var combos = [data.filters.year, data.filters.completion];
        if (data.filters.organisation !== undefined){
            combos.push(data.filters.organisation);
        }
        var final = {
            tab: data.tab,
            table: '#commits-table',
            cb: data.filters.area.concat(data.filters.status),
            combo: combos,
            text: {
                id: '#free-text-search',
                value: data.filters.q
            }
        };

        return final;
    },
    processFromStorageToUsers: function(data){
        var final = {
            tab: data.tab,
            table: '#users-table',
            cb: data.filters.status,
            combo: [data.filters.organisation],
            text: {
                id: '#free-user-search',
                value: data.filters.q
            }
        };

        return final;
    },
    processFromStorageToOrganisations: function (data) {
        var final = {
            tab: data.tab,
            table: '#org-table',
            cb: data.filters.type,
            text: {
                id: '#free-org-search',
                value: data.filters.q
            }
        };

        return final;
    }
};