let myChart = document.getElementById('miniChart').getContext('2d');
let pieChart = new Chart(myChart, {
    type: 'pie',
    data: {
        labels: ['Gewonnen', 'Verloren', 'Gelijkgespeeld'],
        datasets: [{
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
