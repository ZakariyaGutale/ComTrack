/*
    Copyright 2021 European Commission
    
    Licensed under the EUPL, Version 1.2 only (the "Licence");
    You may not use this work except in compliance with the Licence.
    
    You may obtain a copy of the Licence at:
        https://joinup.ec.europa.eu/software/page/eupl5
    
    Unless required by applicable law or agreed to in writing, software 
    distributed under the Licence is distributed on an "AS IS" basis, 
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either 
    express or implied.
    
    See the Licence for the specific language governing permissions 
    and limitations under the Licence.
*/
var DataHubCharts = function(container, options) {
	
	this._el = null;
	this._dashboard = null;
	this._dashBoardSelection = null
	this._chart = null;
	this._data = null;
	this._options = {
		initGraph: 0	
	};
	this.dom = {};
	
	this.options = { 
		useTooltip: false
	};

	this.colors = {
		selColors: ['#e6194B', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#42d4f4', '#f032e6', '#bfef45', '#fabebe',
					'#469990', '#e6beff', '#9A6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#a9a9a9'],
		theme: {
			cc: '#005cc8',
			mp: '#ff4c02',
			mpa: '#fccd02',
			ms: '#f7931e',
			sbe: '#2f9eea',
			sf: '#77bc1f'
		}
	};

	var now = Date.now();

	this.stats = {
		urlJSStatistics: '/json/statistics.json?nocache=' + now,
		data: {}
	};
	
	this.loadStats = function(){
		var self = this;
		var xmlhttp = new XMLHttpRequest();

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				self.stats.data = JSON.parse(xmlhttp.responseText);
			}
		};

		xmlhttp.open("GET", this.stats.urlJSStatistics, true);
		xmlhttp.send();
	}
	
	this._construct = function() { 
		
		this._el = container;
		this.loadStats();
		
		if(options) { 
			for(var key in options) {
				if(this.options.hasOwnProperty(key)) { this.options[key] = options[key]; }
			}
		}	

		if(this._el) { 
			// build dashboard layer
			this._dashboard = document.createElement('div');
			this._dashboard.id = 'dashboard';
			this._el.appendChild(this._dashboard);
			
			var div = document.createElement('div');
			div.id = "dashboard-title";
			div.innerHTML = "Statistics";
			this._dashboard.appendChild(div);


			//Commits per theme
			this._dashBoardSelection = document.createElement('p');
			this._dashBoardSelection.innerHTML = "Commitments per theme";
			this._dashBoardSelection.className = "charts-title";
			this._dashboard.appendChild(this._dashBoardSelection);

			this._dashBoardSelection = document.createElement('div');
			this._dashBoardSelection.id = "commits-per-theme";
			this._dashboard.appendChild(this._dashBoardSelection);

			//Commits per applicants type
			this._dashBoardSelection = document.createElement('p');
			this._dashBoardSelection.innerHTML = "Commitments per organisation type";
			this._dashBoardSelection.className = "charts-title";
			this._dashboard.appendChild(this._dashBoardSelection);

			this._dashBoardSelection = document.createElement('div');
			this._dashBoardSelection.id = "commits-per-applicant-type";
			this._dashboard.appendChild(this._dashBoardSelection);

			//Commits progress
			this._dashBoardSelection = document.createElement('p');
			this._dashBoardSelection.innerHTML = "Commitments progress";
			this._dashBoardSelection.className = "charts-title";
			this._dashboard.appendChild(this._dashBoardSelection);

			this._dashBoardSelection = document.createElement('div');
			this._dashBoardSelection.id = "commits-progress";
			this._dashboard.appendChild(this._dashBoardSelection);

			//Commits progress per theme
			this._dashBoardSelection = document.createElement('p');
			this._dashBoardSelection.innerHTML = "Commitments progress per theme";
			this._dashBoardSelection.className = "charts-title";
			this._dashboard.appendChild(this._dashBoardSelection);

			this._dashBoardSelection = document.createElement('div');
			this._dashBoardSelection.id = "commits-progress-per-theme";
			this._dashboard.appendChild(this._dashBoardSelection);

			//Commits progress per appplicants type
			this._dashBoardSelection = document.createElement('p');
			this._dashBoardSelection.innerHTML = "Commitments progress per organisation type";
			this._dashBoardSelection.className = "charts-title";
			this._dashboard.appendChild(this._dashBoardSelection);

			this._dashBoardSelection = document.createElement('div');
			this._dashBoardSelection.id = "commits-progress-per-applicant-type";
			this._dashboard.appendChild(this._dashBoardSelection);

            //Commits per theme per year
            this._dashBoardSelection = document.createElement('p');
            this._dashBoardSelection.innerHTML = "Commitments per theme per year";
            this._dashBoardSelection.className = "charts-title";
            this._dashboard.appendChild(this._dashBoardSelection);

            this._dashBoardSelection = document.createElement('div');
            this._dashBoardSelection.id = "commits-per-theme-per-year";
            this._dashboard.appendChild(this._dashBoardSelection);

			//Commits per organisation type per year
			this._dashBoardSelection = document.createElement('p');
			this._dashBoardSelection.innerHTML = "Commitments per organisation type per year";
			this._dashBoardSelection.className = "charts-title last-title";
			this._dashboard.appendChild(this._dashBoardSelection);

			this._dashBoardSelection = document.createElement('div');
			this._dashBoardSelection.id = "commits-per-applicant-type-per-year";
			this._dashboard.appendChild(this._dashBoardSelection);
		}
		
		this.hide();
	};

	this.getPropertyValuesArray = function(data, prop, additionalText){
		var values = [];
		for (var i = 0; i < data.length; i++) {
			var value = data[i][prop];
			if (additionalText !== undefined){
				value += additionalText;
			}
			values.push(value);
		}

		return values;
	};

	this.getColorPatternArray = function(data, colorProp, dataProp){
		var pattern = [];
		var colorIdx = 0;
		for (var i = 0; i < data.length; i++) {
			var targetProp = data[i][dataProp];
			if (targetProp.toLowerCase() in this.colors.theme){
				pattern.push(this.colors[colorProp][targetProp.toLowerCase()]);
			} else {
				pattern.push(this.colors.selColors[colorIdx]);
				colorIdx += 1;
			}
		}

		return pattern;
	}

	this.refresh = function (data, filters, terminate){
		this.commitsPerTheme(this.stats.data.commits_per_theme);
		this.commitsPerOrgType(this.stats.data.commits_per_applicant_type);
		this.commitsProgress(this.stats.data.commits_progress);
		this.commitsProgressPerTheme(this.stats.data.commits_progress_per_theme);
		this.commitsProgressPerOrgType(this.stats.data.commits_progress_per_applicant_type);
        this.commitsPerThemePerYear(this.stats.data.commits_per_theme_per_year);
		this.commitsPerApplicantPerYear(this.stats.data.commits_per_applicant_type_per_year);
	};

	this.makeTooltip = function(d, $$, config, defaultTitleFormat, defaultValueFormat, color, useFormat, useTitle){
		var valueFormat = config.tooltip_format_value || defaultValueFormat,
			nameFormat = config.tooltip_format_name || function (name) { return name; },
			text, i, title, value, name, bgcolor;

		if (useTitle){
			var titleFormat = config.tooltip_format_title || defaultTitleFormat;
		}

		for (i = 0; i < d.length; i++) {
			if (! (d[i] && (d[i].value || d[i].value === 0))) { continue; }

			if (useTitle && ! text) {
				title = titleFormat ? titleFormat(d[i].x) : d[i].x;
				text = "<table class='" + $$.CLASS.tooltip + "'>" + (title || title === 0 ? "<tr><th colspan='2'>" + title + "</th></tr>" : "");
			}

			name = nameFormat(d[i].name);
			if (useFormat){
				value = valueFormat(d[i].value, d[i].ratio, d[i].id, d[i].index);
			} else {
				value = d[i].value;
			}

			bgcolor = $$.levelColor ? $$.levelColor(d[i].value) : color(d[i].id);

			if (useTitle){
				text += "<tr class='" + $$.CLASS.tooltipName + "-" + d[i].id + "'>";
				text += "<td class='custom-name'><span style='background-color:" + bgcolor + "'></span>" + name + "</td>";
				text += "<td class='value'>" + value + "</td>";
			} else {
				text = "<table class='" + $$.CLASS.tooltip + "'>"
				text += "<tr class='" + $$.CLASS.tooltipName + "-" + d[i].id + "'>";

				text += "<td class='value'>" + value;
				if (value === 1){
					text += " commitment</td>";
				} else {
					text += " commitments</td>";
				}
			}
			text += "</tr>";
		}
		return text + "</table>";
	};

	this.commitsPerTheme = function(data){
		var self = this;
		var colors = this.getColorPatternArray(data, 'theme', 'id');
		var chart = c3.generate({
			bindto: '#commits-per-theme',
			color: {
				pattern: colors
			},
			size: {
				height: 300
			},
			data: {
				type: 'bar',
				json: data,
				keys: {
					value: ['total']
				},
				color: function(inColor, data) {
					if(data.index !== undefined) {
						return colors[data.index];
					}
					return inColor;
				}
			},
			bar: {
				width: {
					ratio: 0.5
				}
			},
			axis: {
				x: {
					type: 'category',
					categories: this.getPropertyValuesArray(data, 'name'),
					height: 60,
					tick: {
						multiline: true
					}
				},
				y: {
					label: {
						text: '# Commitments',
						position: 'outer-middle'
					},
					tick: {
						format: function(x) {
							if (x !== Math.floor(x)) {
								var tick = d3.selectAll('.c3-axis-y g.tick');
								var subticks = tick.filter(function(val){
									return val === x;
								});
								subticks.style('opacity', 0);
								return '';
							}

							return x;
						}
					}
				}
			},
			legend: {
				show: false
			},
			tooltip: {
				contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
					var $$ = this, config = $$.config;
					return self.makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, true, false);
				}
			}
		});
	};

	this.commitsPerOrgType = function(data){
		var self = this;
		var labels = [];
		var formatData = {};
		data.forEach(function(e) {
			labels.push(e.name);
			formatData[e.name] = e.total;
		});

		var chart = c3.generate({
			bindto: '#commits-per-applicant-type',
			size: {
				height: 300
			},
			data: {
				type: 'pie',
				json: [formatData],
				keys: {
					value: labels
				}
			},
			tooltip: {
				contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
					var $$ = this, config = $$.config;

					return self.makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, false, false);
				}
			}
		});
	};

	this.commitsProgress = function(data){
		var self = this;
		var chart = c3.generate({
			bindto: '#commits-progress',
			size: {
				height: 300
			},
			data: {
				type: 'bar',
				json: data,
				keys: {
					value: ['total']
				}
			},
			bar: {
				width: {
					ratio: 0.5
				}
			},
			axis: {
				x: {
					type: 'category',
					categories: ['0%','25%','50%','75%','100%'],
					height: 60
				},
				y: {
					label: {
						text: '# Commitments',
						position: 'outer-middle'
					},
					tick: {
						format: function(x) {
							if (x !== Math.floor(x)) {
								var tick = d3.selectAll('.c3-axis-y g.tick');
								var subticks = tick.filter(function(val){
									return val === x;
								});
								subticks.style('opacity', 0);
								return '';
							}

							return x;
						}
					}
				}
			},
			legend: {
				show: false
			},
			tooltip: {
				contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
					var $$ = this, config = $$.config;
					return self.makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, true, false);
				}
			}
		});
	};

	this.getColumnsProgress = function (data) {
		var columns = [];
		for (var i = 0; i < data.length; i++) {
			columns.push([
				data[i].name,
				data[i].p0,
				data[i].p25,
				data[i].p50,
				data[i].p75,
				data[i].p100
			]);
		}

		return columns;
	};

	this.commitsProgressPerTheme = function (data) {
		var self = this;
		var colors = this.getColorPatternArray(data, 'theme', 'id');
		var chart = c3.generate({
			bindto: '#commits-progress-per-theme',
			size: {
				height: 300
			},
			color: {
				pattern: colors
			},
			data: {
				type: 'bar',
				columns: this.getColumnsProgress(data)
			},
			bar: {
				width: {
					ratio: 0.5
				}
			},
			axis: {
				x: {
					type: 'category',
					categories: ['0%','25%','50%','75%','100%'],
					height: 60
				},
				y: {
					label: {
						text: '# Commitments',
						position: 'outer-middle'
					},
					tick: {
						format: function(x) {
							if (x !== Math.floor(x)) {
								var tick = d3.selectAll('.c3-axis-y g.tick');
								var subticks = tick.filter(function(val){
									return val === x;
								});
								subticks.style('opacity', 0);
								return '';
							}

							return x;
						}
					}
				}
			},
			tooltip: {
				contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
					var $$ = this, config = $$.config;
					return self.makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, true, true);
				}
			}
		});
	};

	this.commitsProgressPerOrgType = function (data) {
		var self = this;
		var chart = c3.generate({
			bindto: '#commits-progress-per-applicant-type',
			size: {
				height: 300
			},
			data: {
				type: 'bar',
				columns: this.getColumnsProgress(data)
			},
			bar: {
				width: {
					ratio: 0.5
				}
			},
			axis: {
				x: {
					type: 'category',
					categories: ['0%','25%','50%','75%','100%'],
					height: 60
				},
				y: {
					label: {
						text: '# Commitments',
						position: 'outer-middle'
					},
					tick: {
						format: function(x) {
							if (x !== Math.floor(x)) {
								var tick = d3.selectAll('.c3-axis-y g.tick');
								var subticks = tick.filter(function(val){
									return val === x;
								});
								subticks.style('opacity', 0);
								return '';
							}

							return x;
						}
					}
				}
			},
			tooltip: {
				contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
					var $$ = this, config = $$.config;
					return self.makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, true, true);
				}
			}
		});
	};

	this.getColumnsYear = function (data) {
		var columns = [];
		for (var i = 0; i < data.years.length; i++) {
			var column = [data.years[i]]
			for (var j = 0; j < data.data.length; j++) {
				column.push(data.data[j]['y' + data.years[i]]);
			}
			columns.push(column);
		}

		return columns;
	}

	this.calculateChartHeight = function(data){
		return data.categories.length * (20 * data.years.length) + 100;
	};
	
	this.commitsPerThemePerYear = function (data) {
		var self = this;

        var chart = c3.generate({
            bindto: '#commits-per-theme-per-year',
			padding: {
            	bottom: 20
			},
            size: {
				/*height: this.calculateChartHeight(data)*/
				height: 300
            },
            data: {
                type: 'bar',
                columns: this.getColumnsYear(data)
            },
            axis: {
            	/*rotated: true,*/
                x: {
                    type: 'category',
                    categories: data.categories,
                },
                y: {
                    label: {
                        text: '# Commitments',
                        /*position: 'outer-center'*/
						position: 'outer-middle'
                    },
                    tick: {
                        format: function(x) {
                            if (x !== Math.floor(x)) {
                                var tick = d3.selectAll('.c3-axis-y g.tick');
                                var subticks = tick.filter(function(val){
                                    return val === x;
                                });
                                subticks.style('opacity', 0);
                                return '';
                            }

                            return x;
                        }
                    }
                }
            },
			tooltip: {
				contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
					var $$ = this, config = $$.config;
					return self.makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, true, true);
				}
			},
			legend: {
				position: 'right'
			}
        });
    };

	this.commitsPerApplicantPerYear = function (data) {
		var self = this;
		var chart = c3.generate({
			bindto: '#commits-per-applicant-type-per-year',
			padding: {
				bottom: 20
			},
			size: {
				/*height: this.calculateChartHeight(data)*/
				height: 300
			},
			data: {
				type: 'bar',
				columns: this.getColumnsYear(data)
			},
			axis: {
				/*rotated: true,*/
				x: {
					type: 'category',
					categories: data.categories,
				},
				y: {
					label: {
						text: '# Commitments',
						/*position: 'outer-center'*/
						position: 'outer-middle'
					},
					tick: {
						format: function(x) {
							if (x !== Math.floor(x)) {
								var tick = d3.selectAll('.c3-axis-y g.tick');
								var subticks = tick.filter(function(val){
									return val === x;
								});
								subticks.style('opacity', 0);
								return '';
							}

							return x;
						}
					}
				}
			},
			tooltip: {
				contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
					var $$ = this, config = $$.config;
					return self.makeTooltip(d, $$, config, defaultTitleFormat, defaultValueFormat, color, true, true);
				}
			},
			legend: {
				position: 'right'
			}
		});
	};

	this.hide = function( ) {
		this._el.style.visibility = "hidden";
		this._el.style.display = "none";
		
		
	};
	
	this.show = function( ) {
		this._el.style.visibility = "visible";
		this._el.style.display = "inline";
		
	};

	this._construct();
};

// Create the event
var event = new CustomEvent("serviceLoaded", { "detail": {"service": "charts", "object": DataHubCharts}});

// Dispatch/Trigger/Fire the event
document.dispatchEvent(event);
