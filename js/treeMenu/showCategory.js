  function show(subCategory, path) {
    var thesub;
    var theimage;
    var imageid;
    var testingdiv;
    thesub = document.getElementById(subCategory);
    imageid = "img_"+subCategory;
    theimage = document.getElementById(imageid);
    testingdiv = document.getElementById("testing");
    if(thesub.style.display == 'block') {
      //collapse...
      thesub.style.display = 'none';
      theimage.src = path+'/images/recursion/images/c.gif';
    }
    else {
      thesub.style.display = 'block';
      theimage.src = path+'/images/recursion/images/e.gif';
    }
  }
