  var num1 = 0;
  var num2 = 0;
  var num3 = 0;
  var counter1 = 0;
  var counter2 = 0;
  var counter3 = 0;

  var elem1 = document.getElementById("updateNum1");
  setInterval(change1, 50);

  function change1() {
    if (num1 <= 31) {
      elem1.innerHTML = num1.toString();
      num1++;
      counter1 = 0;
    }
  }

  var elem2 = document.getElementById("updateNum2");
  setInterval(change2, 8);

  function change2() {
    if (num2 <= 714) {
      elem2.innerHTML = num2.toString();
      num2++;
      counter2 = 0;
    }
  }

  var elem3 = document.getElementById("updateNum3");
  setInterval(change3, 50);

  function change3() {
    if (num3 <= 7) {
      elem3.innerHTML = num3.toString();
      num3++;
      counter3 = 0;
    }
  }