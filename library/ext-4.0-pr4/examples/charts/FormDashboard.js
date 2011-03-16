Ext.require([
    'Ext.form.*',
    'Ext.data.*',
    'Ext.chart.*',
    'Ext.grid.GridPanel',
    'Ext.layout.container.Column'
]);


Ext.onReady(function(){

    var bd = Ext.getBody(),
        form = false,
        rec = false,
        selectingRow = false,
        selectedStoreItem = false,
        //performs the highlight of an item in the bar series
        selectItem = function(storeItem) {
            var name = storeItem.get('company'),
                series = barChart.series.get(0),
                i, items, l;
            
            series.highlight = true;
            series.unHighlightItem();
            series.cleanHighlights();
            for (i = 0, items = series.items, l = items.length; i < l; i++) {
                if (name == items[i].storeItem.get('company')) {
                    selectedStoreItem = items[i].storeItem;
                    series.highlightItem(items[i]);
                    break;
                }
            }
            series.highlight = false;
        },
        //updates a record modified via the form
        updateRecord = function(rec) {
            var name, series, i, l, items, json = [{
                'Name': 'Price %',
                'Data': rec.get('price %')
            }, {
                'Name': 'Revenue %',
                'Data': rec.get('revenue %')
            }, {
                'Name': 'Growth %',
                'Data': rec.get('growth %')
            }, {
                'Name': 'Product %',
                'Data': rec.get('product %')
            }, {
                'Name': 'Market %',
                'Data': rec.get('market %')
            }];
            chs.loadData(json);
            selectItem(rec);
        },
        createListeners = function() {
            var timer = null;
            return {
                change: function(field, newValue, oldValue, listener) {
                    var name, series;
                    if (selectingRow) return;
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        form.updateRecord(rec);
                        updateRecord(rec);
                    }, 1000);
                }
            };
        };
        
    // sample static data for the store
    var myData = [
        ['3m Co'],
        ['Alcoa Inc'],
        ['Altria Group Inc'],
        ['American Express Company'],
        ['American International Group, Inc.'],
        ['AT&T Inc'],
        ['Boeing Co.'],
        ['Caterpillar Inc.'],
        ['Citigroup, Inc.'],
        ['E.I. du Pont de Nemours and Company'],
        ['Exxon Mobil Corp'],
        ['General Electric Company'],
        ['General Motors Corporation'],
        ['Hewlett-Packard Co'],
        ['Honeywell Intl Inc'],
        ['Intel Corporation'],
        ['International Business Machines'],
        ['Johnson & Johnson'],
        ['JP Morgan & Chase & Co'],
        ['McDonald\'s Corporation'],
        ['Merck & Co., Inc.'],
        ['Microsoft Corporation'],
        ['Pfizer Inc'],
        ['The Coca-Cola Company'],
        ['The Home Depot, Inc.'],
        ['The Procter & Gamble Company'],
        ['United Technologies Corporation'],
        ['Verizon Communications'],
        ['Wal-Mart Stores, Inc.']
    ];
    
    for (var i = 0, l = myData.length, rand = Math.random; i < l; i++) {
        var data = myData[i];
        data[1] = ((rand() * 10000) >> 0) / 100;
        data[2] = ((rand() * 10000) >> 0) / 100;
        data[3] = ((rand() * 10000) >> 0) / 100;
        data[4] = ((rand() * 10000) >> 0) / 100;
        data[5] = ((rand() * 10000) >> 0) / 100;
    }

    //create data store to be shared among the grid and bar series.
    var ds = Ext.create('Ext.data.ArrayStore', {
        fields: [
            {name: 'company'},
            {name: 'price %',   type: 'float'},
            {name: 'revenue %', type: 'float'},
            {name: 'growth %',  type: 'float'},
            {name: 'product %', type: 'float'},
            {name: 'market %',  type: 'float'},
        ],
        data: myData
    });
    
    //create radar dataset model.
    var chs = Ext.create('Ext.data.JsonStore', {
        fields: ['Name', 'Data'],
        data: [
        {
            'Name': 'Price %',
            'Data': 10
        }, {
            'Name': 'Revenue %',
            'Data': 10
        }, {
            'Name': 'Growth %',
            'Data': 10
        }, {
            'Name': 'Product %',
            'Data': 10
        }, {
            'Name': 'Market %',
            'Data': 10
        }]
    });
    
    //Radar chart will render information for a selected company in the
    //list. Selection can also be done via clicking on the bars in the series.
    var radarChart = Ext.create('Ext.chart.Chart', {
        margin: '0 0 0 0',
        insetPadding: 20,
        flex: 1.2,
        animate: true,
        store: chs,
        theme: 'Category1',
        axes: [{
            type: 'Radial',
            position: 'radial',
            label: {
                display: 'none'    
            }
        }],
        series: [{
            type: 'radar',
            xField: 'Name',
            yField: 'Data',
            showInLegend: false,
            showMarkers: true,
            markerCfg: {
                radius: 2,
                size: 2
            },
            label: {
                display: true,
                field: 'Name'
            },
            style: {
                'opacity': 0.5,
                'stroke-width': 0.5
            }
        }]
    });
    
    //create a grid that will list the dataset items.
    var gridPanel = Ext.create('Ext.grid.GridPanel', {
        id: 'company-form',
        flex: 0.60,
        store: ds,
        title:'Company Data',

        headers: [
            {
                id       :'company',
                text   : 'Company',
                flex: 1,
                sortable : true,
                dataIndex: 'company'
            },
            {
                text   : 'Price',
                width    : 75,
                sortable : true,
                dataIndex: 'price %',
                renderer: perc
            },
            {
                text   : 'Revenue',
                width    : 75,
                sortable : true,
                dataIndex: 'revenue %',
                renderer: perc
            },
            {
                text   : 'Growth',
                width    : 75,
                sortable : true,
                dataIndex: 'growth %',
                renderer: perc
            },
            {
                text   : 'Product',
                width    : 75,
                sortable : true,
                dataIndex: 'product %',
                renderer: perc
            },
            {
                text   : 'Market',
                width    : 75,
                sortable : true,
                dataIndex: 'market %',
                renderer: perc
            }
        ],

        listeners: {
            selectionchange: function(model, records) {
                var json, name, i, l, items, series;
                if (records[0]) {
                    rec = records[0];
                    selectingRow = true;
                    form = form || this.up('form').getForm();
                    form.loadRecord(rec);
                    selectingRow = false;
                    updateRecord(rec);
                }
            }
        }
    });

    //create a bar series to be at the top of the panel.
    var barChart = Ext.create('Ext.chart.Chart', {
        flex: 1,
        shadow: true,
        animate: true,
        store: ds,
        theme: 'Category1',
        axes: [{
            type: 'Numeric',
            position: 'left',
            fields: ['price %'],
            minimum: 0,
            label: {
                font: '9px Arial'
            },
            hidden: true
        }, {
            type: 'Category',
            position: 'bottom',
            fields: ['company'],
            label: {
                font: '9px Arial'
            }
        }],
        series: [{
            type: 'column',
            axis: 'left',
            highlight: true,
            highlightCfg: {
                fill: '#99d',
                stroke: '#555',
                'stroke-width': 0.5
            },
            label: {
              display: 'insideEnd',
                field: 'price %',
                orientation: 'horizontal',
                color: '#333',
              'text-anchor': 'middle'
            },
            xField: 'name',
            yField: ['price %']
        }]        
    });
    
    //disable highlighting by default.
    barChart.series.get(0).highlight = false;
    
    //add listener to (re)select bar item after sorting or refreshing the dataset.
    barChart.addListener('beforerefresh', (function() {
        var timer = false;
        return function() {
            clearTimeout(timer);
            if (selectedStoreItem) {
                timer = setTimeout(function() {
                    selectItem(selectedStoreItem);
                }, 900);
            }
        };
    })());
    
    //trigger an item selection when clicking on a bar from series.
    barChart.on({
        'itemmouseup': function(item) {
             var series = barChart.series.get(0),
                 index = Ext.Array.indexOf(series.items, item),
                 selectionModel = gridPanel.getSelectionModel();
             
             selectedStoreItem = item.storeItem;
             selectionModel.select(index);
             selectItem(selectedStoreItem);
        }    
    });

    //use a renderer for values in the data view.
    function perc(v) {
        return v + '%';
    }

    /*
     * Here is where we create the Form
     */
    var gridForm = Ext.create('Ext.form.FormPanel', {
        title: 'Company data',
        frame: true,
        bodyPadding: 5,
        width: 870,
        height: 700,

        fieldDefaults: {
            labelAlign: 'left',
            msgTarget: 'side'
        },
    
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        
        items: [barChart, {
            
            layout: {type: 'hbox', align: 'stretch'},
            flex: 3,

            items: [gridPanel, {
                flex: 0.4,
                layout: {type: 'vbox', align:'stretch'},
                title: 'Company Details',
                items: [{
                    margin: '5',
                    xtype: 'fieldset',
                    flex: 1,
                    title:'Company details',
                    defaults: {
                        width: 240,
                        labelWidth: 90
                    },
                    defaultType: 'textfield',
                    items: [{
                        fieldLabel: 'Name',
                        name: 'company',
                        disabled: true
                    },{
                        fieldLabel: 'Price %',
                        name: 'price %',
                        listeners: createListeners('price %')
                    },{
                        fieldLabel: 'Revenue %',
                        name: 'revenue %',
                        listeners: createListeners('revenue %')
                    },{
                        fieldLabel: 'Growth %',
                        name: 'growth %',
                        listeners: createListeners('growth %')
                    },{
                        fieldLabel: 'Product %',
                        name: 'product %',
                        listeners: createListeners('product %')
                    },{
                        fieldLabel: 'Market %',
                        name: 'market %',
                        listeners: createListeners('market %')
                    }]
                }, radarChart]
            }]
        }],
        renderTo: bd
    });

    var gp = Ext.getCmp('company-form');
});