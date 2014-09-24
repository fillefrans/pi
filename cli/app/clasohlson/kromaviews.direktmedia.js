var views = views || { widgetprefix : "views-widget-", charts : {} };

views.overlay = {
		color: "#2D2101",
		opacity: 0.15
};

//views.colors. = ['#F44165', '#FA3A49', '#FD502B', '#FFBF20', '#9FC808', '#DEE1E8', '#D1D3DA', '#FF6A00', '#6D0074', '#6AF9C4'];
				
views.direktmedia = {
	service: "direktinfo",
	client: "link",
	campaign: "ellos",
	object: "sms",
	reference: {
			population: {
				total : 4985870,
				counties :[0, //dummy
						278352, // Østfold
						556254, // Akershus
						613285, // Oslo
						192791, // Hedmark
						187147, // Oppland
						265164, // Buskerud
						236424, // Vestfold
						170023, // Telemark
						111495, // Aust-Agder
						174324, // Vest-Agder
						443115, // Rogaland
						490570, // Hordaland
								 0, // 13 fins ikke
						108201, // Sogn og fjordane
						256628, // møre og romsdal
						297950, // sør-trøndelag
						133390, // nord-trøndelag
						238320, // nordland
						158650, // troms
						 73787, // finnmark
							2600 	// svalbard
				]
			}
	},
	lifePhase : [
		{
			index : 0,
			name: "00",
			description: "Dummy"
		},
		{
			index : 1,
			name: "Ungdom og studenter",
			description: "18-24 år, uten barn"
		},
		{
			index : 2,
			name: "Single",
			description: "25-49 år, singel, uten barn"
		},
		{
			index : 3,
			name: "Samboere",
			description: "25-49 år, samboer/gift, uten barn"
		},
		{
			index : 4,
			name: "Småbarnsfamilier",
			description: "19-54 år, familie med barn 0-5 år"
		},
		{
			index : 5,
			name: "Veletablerte familier",
			description: "19-54 år, familie med barn 6-17 år"
		},
		{
			index : 6,
			name: "Middelaldrende",
			description: "50-64 år, uten hjemmeboende barn"
		},
		{
			index : 7,
			name: "Seniorer",
			description: "65+ år"
		}
	], // end lifephase array
	CategoryConsumerStrength :[
		{
			index : 0,
			name: "00",
			description: "Dummy"
		},
		{
			index : 1,
			name: "Lav",
			description: "Lav"
		},
		{
			index : 2,
			name: "Middels",
			description: "Middels"
		},
		{
			index : 3,
			name: "Høy",
			description: "Høy"
		}
	],
	FISIncome : [
		{
			index : 0,
			name: "00",
			description: "Dummy"
		},
		{
			index : 1,
			name: "0-150''",
			description: "0 - 150.000 kr"
		},
		{
			index : 2,
			name: "150-200",
			description: "150.000 - 200.000 kr"
		},
		{
			index : 3,
			name: "200-250",
			description: "200.000 - 250.000 kr"
		},
		{
			index : 4,
			name: "250-300",
			description: "250.000 - 300.000 kr"
		},
		{
			index : 5,
			name: "300-350",
			description: "300.000 - 350.000 kr"
		},
		{
			index : 6,
			name: "350-400",
			description: "350.000 - 400.000 kr"
		},
		{
			index : 7,
			name: "400-450",
			description: "400.000 - 450.000 kr"
		},
		{
			index : 8,
			name: "450-500",
			description: "450.000 - 500.000 kr"
		},
		{
			index : 9,
			name: "500-600",
			description: "500.000 - 600.000 kr"
		},
		{
			index : 10,
			name: "600-700",
			description: "600.000 - 700.000 kr"
		},
		{
			index : 11,
			name: "700+",
			description: "Over 700.000 kr"
		}
	],
	FISWealth :[
		{
			index : 0,
			name: "00",
			description: "dummy"
		},
		{
			index : 1,
			name: "0",
			description: "0 kr"
		},
		{
			index : 2,
			name: "001-025",
			description: "1.000 - 25.000 kr"
		},
		{
			index : 3,
			name: "025-100",
			description: "25.000 - 100.000 kr"
		},
		{
			index : 4,
			name: "100-250",
			description: "100.000 - 250.000 kr"
		},
		{
			index : 5,
			name: "250-500",
			description: "250.000 - 500.000 kr"
		},
		{
			index : 6,
			name: "500-750",
			description: "500.000 - 750.000 kr"
		},
		{
			index : 7,
			name: "750+",
			description: "Over 750.000 kr"
		}
	],
	FISEducation :[
		{
			index : 0,
			name: "00",
			description: "dummy"
		},
		{
			index : 1,
			name: "Lavt",
			description: "Lavt utdanningsnivå"
		},
		{
			index : 2,
			name: "Noe lavt",
			description: "Noe lavt utdanningsnivå"
		},
		{
			index : 3,
			name: "Middels",
			description: "Middels utdanningsnivå"
		},
		{
			index : 4,
			name: "Noe høyt",
			description: "Noe høyt utdanningsnivå"
		},
		{
			index : 5,
			name: "Høyt",
			description: "Høyt utdanningsnivå"
		}
	],
	CategoryUrban :[
		{
			index : 0,
			name: "00",
			description: "dummy"
		},
		{
			index : 1,
			name: "By",
			description: ""
		},
		{
			index : 2,
			name: "Tettsted",
			description: ""
		},
		{
			index : 3,
			name: "Land",
			description: ""
		}
	],
	sex :{
		M : {
			index : 0,
			name: "Menn",
			description: "Menn"
			},
		K : {
			index : 1,
			name: "Kvinner",
			description: "Kvinner"
			},
		U : {
			index : 2,
			name: "Ukjent",
			description: "Ukjent"
		}
	}
};