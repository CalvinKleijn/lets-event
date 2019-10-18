let myChart = document.getElementById('myChart').getContext('2d');
let barChart = new Chart(myChart, {
    type: 'bar',
    data: {
        labels: ['Gewonnen', 'Verloren', 'Gelijkgespeeld'],
        datasets: [{
            label: 'toernooi uitslagen',
            data: [
                '66',
                '33',
                '1',
            ],
            backgroundColor: [
                'green',
                'red',
                'blue'
            ],
            borderWith: 1,
            borderColor: '#ffffff',
            hoverBorderWith: 3,
            hoverborderColor: '#000'
        }]
    },
    options: {
        title: {
            // display: true,
            // text: "Toernooi Uitslagen",
            // fontSize: 25
        },
        legend: {
            position: 'top'
        }
    }
});