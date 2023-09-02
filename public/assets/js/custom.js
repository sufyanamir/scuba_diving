
window.onload = function() {

  var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      theme: "light2", // "light1", "light2", "dark1", "dark2"
      title: {
          text: "Top Oil Reserves"
      },
      axisY: {
          title: "Reserves(MMbbl)"
      },
      data: [{
          type: "column",
          showInLegend: true,
          legendMarkerColor: "grey",
          legendText: "MMbbl = one million barrels",
          dataPoints: [{
                  y: 300878,
                  label: "Venezuela"
              },
              {
                  y: 266455,
                  label: "Saudi"
              },
              {
                  y: 169709,
                  label: "Canada"
              },
              {
                  y: 158400,
                  label: "Iran"
              },
              {
                  y: 142503,
                  label: "Iraq"
              },
              {
                  y: 101500,
                  label: "Kuwait"
              },
              {
                  y: 97800,
                  label: "UAE"
              },
              {
                  y: 80000,
                  label: "Russia"
              }
          ]
      }]
  });
  chart.render();

}

//header open close
function openNav() {
    document.getElementById("mySidebar").style.width = "200px";
    document.getElementById("main-panel").style.marginLeft = "200px";
    document.getElementById("main-panel").style.borderRadius = "30px 0 0 30px";
    document.getElementById("closebtn").style.display = "block";
    document.getElementById("openbtn").style.display = "none";
    // document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
    document.getElementById("main-panel").style.marginLeft = "0";
    document.getElementById("main-panel").style.borderRadius = "0";
    document.getElementById("closebtn").style.display = "none";
    document.getElementById("openbtn").style.display = "block";
}
//header open close




// data Table
    new DataTable('#myTable');
// data Table