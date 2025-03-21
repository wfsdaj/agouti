let hamburger = document.querySelector('.dropbtn');
if (hamburger) {
  let menu = document.querySelector('.dr-menu');
  const toggleMenu = () => {
    menu.classList.toggle('block');
  };
  hamburger.addEventListener('click', e => {
    e.stopPropagation();
    toggleMenu();
  });
  document.addEventListener('click', e => {
    let target = e.target;
    let its_menu = target == menu || menu.contains(target);
    let its_hamburger = target == hamburger;
    let menu_is_active = menu.classList.contains('block');

    if (!its_menu && !its_hamburger && menu_is_active) {
      toggleMenu();
    }
  });
}

// Цвет обложки для профиля
let colorPicker = document.getElementById("colorPicker");
if (colorPicker) {
  let box = document.getElementById("box");
  let color = document.getElementById("color");

  box.style.borderColor = colorPicker.value;

  colorPicker.addEventListener("input", function (event) {
    box.style.borderColor = event.target.value;
  }, false);

  colorPicker.addEventListener("change", function (event) {
    color.value = colorPicker.value;
  }, false);
}

// Subscribe to a topic / post
document.querySelectorAll(".focus-id")
  .forEach(el => el.addEventListener("click", function (e) {
    let content_id = el.dataset.id;
    let type_content = el.dataset.type;

    fetch("/focus/" + type_content, {
      method: "POST",
      body: "content_id=" + content_id,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then((response) => {
        return;
      }).then((text) => {
        location.reload();
      });
  }));

// Up
document.querySelectorAll(".up-id")
  .forEach(el => el.addEventListener("click", function (e) {
    let up_id = el.dataset.id;
    let type_content = el.dataset.type;
    let count = el.dataset.count;
    fetch("/votes/" + type_content, {
      method: "POST",
      body: "up_id=" + up_id,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then((response) => {
        return;
      }).then((text) => {
        let new_cont = (parseInt(count) + parseInt(1));
        let upVot = document.querySelector('#up' + up_id + '.voters');
        let upScr = document.querySelector('#up' + up_id).querySelector('.score');
        upVot.classList.add('blue');
        upScr.replaceWith(new_cont);
      });
  }));

// Add a post to your profile
document.querySelectorAll(".add-post-profile")
  .forEach(el => el.addEventListener("click", function (e) {
    let post_id = el.dataset.post;
    fetch("/post/add/profile", {
      method: "POST",
      body: "post_id=" + post_id,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then((response) => {
        return;
      }).then((text) => {
        let mPost = document.querySelector('.add-post-profile').querySelector('.mu_post');
        mPost.replaceWith('+++');
      });
  }));

// Delete a post from your profile
document.querySelectorAll(".del-post-profile")
  .forEach(el => el.addEventListener("click", function (e) {
    let post_id = el.dataset.post;
    fetch("/post/delete/profile", {
      method: "POST",
      body: "post_id=" + post_id,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then((response) => {
        return;
      }).then((text) => {
        location.reload();
      });
  }));

// Recommend a post
document.querySelectorAll(".post-recommend")
  .forEach(el => el.addEventListener("click", function (e) {
    let post_id = el.dataset.id;
    fetch("/post/recommend", {
      method: "POST",
      body: "post_id=" + post_id,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then((response) => {
        return;
      }).then((text) => {
        location.reload();
      });
  }));

// Add / Remove from favorites
document.querySelectorAll(".add-favorite")
  .forEach(el => el.addEventListener("click", function (e) {
    let content_id = el.dataset.id;
    let content_type = el.dataset.type;
    let front = el.dataset.front;
    fetch("/favorite/" + content_type, {
      method: "POST",
      body: "content_id=" + content_id,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then((response) => {
        return;
      }).then((text) => {
        if (front == 'personal') {
          location.reload();
        } else {
          if (content_type == 'post') {
            document.getElementById("favorite_" + content_id).classList.toggle("blue");
          } else {
            document.getElementById("fav-comm_" + content_id).classList.toggle("blue");
          }
        }
      });
  }));

// Deleting / restoring content
document.querySelectorAll(".type-action")
  .forEach(el => el.addEventListener("click", function (e) {
    let content_id = el.dataset.id;
    let content_type = el.dataset.type;
    fetch("/status/action", {
      method: "POST",
      body: "info=" + content_id + "@" + content_type,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then((response) => {
        location.reload();
      })
  }));

// Parsing the title from the site for > TL1
document.querySelectorAll("#graburl")
  .forEach(el => el.addEventListener("click", function (e) {
    let uri = document.getElementById('link').value;

    if (uri === '') {
      return;
    }

    fetch("/post/grabtitle", {
      method: "POST",
      body: "uri=" + uri,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then(function (response) {
        if (!response.ok) {
          // Сервер вернул код ответа за границами диапазона [200, 299]
          return Promise.reject(new Error(
            'Response failed: ' + response.status + ' (' + response.statusText + ')'
          ));
        }
        return response.json();
      }).then(function (data) {
        document.querySelector('input[name=post_title]').value = data.title
        document.querySelector('.ProseMirror').insertAdjacentHTML('afterBegin', data.description);
      }).catch(function (error) {
        // error
      })
  }));

// Edit comment
document.querySelectorAll(".editcomm")
  .forEach(el => el.addEventListener("click", function (e) {
    let comment_id = el.dataset.comment_id;
    let post_id = el.dataset.post_id;
    let comment = document.getElementById("comment_addentry" + comment_id);

    fetch("/comment/editform", {
      method: "POST",
      body: "comment_id=" + comment_id + "&post_id=" + post_id,
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then(
        response => {
          return response.text();
        }
      ).then(
        text => {
          document.getElementById("comment_" + comment_id).classList.add("edit");
          comment.classList.add("block");
          comment.innerHTML = text;

          document.querySelectorAll("#cancel_comment")
            .forEach(el => el.addEventListener("click", function (e) {
              comment.classList.remove("block");
            }));
        }
      );
  }));