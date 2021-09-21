<?php
session_start();
include "../pdoInc.php";

if (!isset($_SESSION['user']['email'])) {
    // 沒登入，滾出去
    die('<meta http-equiv="refresh" content="0; url=../index.php">');
}

?>

<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <script src="../src/library/jquery/jquery.min.js"></script>
    <script src="../src/library/moment-with-locales.min.js"></script>
    <script src="../src/common/common.js"></script>
    <script src="../src/component/dropBox/index.js"></script>
    <link rel="stylesheet" type="text/css" href="../src/common/common.css">
    <link rel="stylesheet" type="text/css" href="../src/component/pop/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/dropBox/index.css">
    <link rel="stylesheet" type="text/css" href="../src/component/datePicker/index.css">
    <link rel="stylesheet" type="text/css" href="index.css?v=<?php echo time(); ?>">
    <title>獎勵</title>
</head>

<body>
    <div id="content">
        <div class="tab">
            <div class="tab-content">
                <div class="tab-content-top">
                    <h1 class="page-title">
                        獎勵
                    </h1>
                    <button class="button-fill upload-award-btn">上傳獎勵</button>
                </div>
                <div class="gallery">
                    <?php
                    $getAward = $dbh->prepare('SELECT * FROM award');
                    $getAward->execute();
                    while ($award = $getAward->fetch(PDO::FETCH_ASSOC)) {
                        $defaultAwardImg = '../src/img/3Dcity.svg';

                        if ($award['img'] != null) {
                            $awardImg = $award['img'];
                        } else if ($award['img_link'] != null) {
                            $awardImg = $award['img_link'];
                        } else {
                            $awardImg = $defaultAwardImg;
                        }

                        echo '<div class="gallery-item">
                        <div class="name">
                        ' . $award['name'] . '
                    </div>
                        <div class="image">
                            <img src="' . $awardImg . '" />
                        </div>
                    </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- tab end-->

        <div id="viewImg" class="pop close">
            <div class="inner">
            </div>
        </div>

        <div id="uploadAward" class="pop close">
            <form class="inner" action="../src/action/addAward.php" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/star-active.svg" alt="icon">
                        <span>上傳獎勵</span>
                    </div>
                    <div class="close">x</div>
                </div>

                <div class="content">
                    <div class="award">
                        <div class="submit-top">
                            <input type="file" name="upload_award_img" id="upload_award_img" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_award_img">
                                <div id="award__img_area" class="img">
                                    <img class="award_submit" src="../src/img/3Dcity.svg" alt="award_submit">
                                </div>
                            </label>
                        </div>
                        <div class="submit-bottom">
                        <div class="form__input award_name">
                                <div class="title">獎勵名稱</div>
                                <input class="input" type="text" name="award_name" placeholder="請輸入獎勵名稱"></input>
                            </div>
                            <div class="form__input award_link">
                                <div class="title">獎勵圖片連結</div>
                                <input class="input" type="text" name="award_link" placeholder="請輸入圖片連結（選填）"></input>
                            </div>
                        </div>
                        <div class="notice">
                            需提交至少一個圖片或連結
                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="submit-homework__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>
        <div id="editHomework" class="pop close">
            <form class="inner" action="../src/action/updateHomework.php" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/mission-dark.svg" alt="icon">
                        <span>編輯作業</span>
                    </div>
                    <div class="close">x</div>
                </div>

                <div class="content">
                    <div class="mission">
                        <div class="submit-top">
                            <input type="file" name="upload_mission_img_update" id="upload_mission_img_update" accept=".jpg, .jpeg, .png, .svg" hidden />
                            <label for="upload_mission_img_update">
                                <div id="mission__img_area_update" class="img">
                                    <?php
                                    if (isset($homework['img']) && isset($homework['imgType'])) {

                                        echo '<img class="mission_submit" src="' . $homework['img'] . '" alt="mission_submit">';
                                    } else {
                                        echo '<img class="mission_submit" src="../src/img/3Dcity.svg" alt="mission_submit">';
                                    }
                                    ?>
                                </div>
                            </label>
                        </div>
                        <div class="submit-bottom">
                            <div class="form__input mission_link">
                                <div class="title">連結</div>
                                <?php
                                if (isset($homework['imgLink'])) {
                                    echo '<input class="input group_at_least_one" type="text" name="submit_mission_link_update" placeholder="請輸入連結" value="' . $homework['imgLink'] . '"></input>';
                                } else {
                                    echo '<input class="input group_at_least_one" type="text" name="submit_mission_link_update" placeholder="請輸入連結"></input>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="notice">
                        需提交至少一個圖片或連結
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="edit-homework__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>
        <div id="deleteHomework" class="pop close">
            <form class="inner" action="" method="post">
                <div class="top">
                    <div class="title">
                        <img class="header__icon" src="../src/img/icon/trash.svg" alt="icon">
                        <span>刪除作業</span>
                    </div>
                    <div class="close">x</div>
                </div>
                <div class="content">
                    <div class="alert">
                        確定要刪除作業嗎？
                    </div>
                </div>
                <div class="buttons">
                    <button class="cancel button button-pink">取消</button>
                    <button type="submit" class="delete-homework__confirm button button-fill">確定</button>
                </div>
            </form>
        </div>


    </div>
    <!-- content end-->
</body>
<script>

</script>
<script src="../src/library/jquery.min.js"></script>
<script src="../src/library/datatables/datatables.min.js"></script>
<script src="../src/library/moment-with-locales.min.js"></script>
<script src="../src/library/daterangepicker/daterangepicker.min.js"></script>
<script src="../src/component/pop/index.js"></script>
<script src="../src/component/sideMenu/index.js"></script>
<script src="../src/common/common.js"></script>
<script src="index.js"></script>

</html>