<?php
/*
Plugin Name: Recent Posts Tracker
Description: 在单篇文章页面记录用户的浏览记录到localStorage，并在页面右下角显示最近访问的5个文章链接，方便用户跳转。
Version: 1.0
Author: Halo Master
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 防止直接访问文件
}

/**
 * 输出浮层及其脚本
 */
function rpt_add_floating_panel() {
    // 仅在单篇文章页显示
    if ( is_single() ) {
        // 获取当前文章信息
        $post_id    = get_the_ID();
        $post_title = get_the_title();
        $post_link  = get_permalink();
        ?>
        <!-- 显示浮层 -->
        <div id="rpt-floating-panel" style="position: fixed; right: 20px; bottom: 20px; background: #fff; border: 1px solid #ccc; padding: 10px; width: 220px; z-index: 10000; box-shadow: 0 0 5px rgba(0,0,0,0.3);">
            <h4 style="margin: 0 0 10px; font-size: 16px;">最近浏览</h4>
            <ul id="rpt-post-list" style="list-style: none; padding: 0; margin: 0;"></ul>
        </div>
        <!-- 脚本处理localStorage记录和展示 -->
        <script type="text/javascript">
        (function(){
            // 当前文章信息
            var currentPost = {
                id: <?php echo (int)$post_id; ?>,
                title: <?php echo json_encode($post_title); ?>,
                link: <?php echo json_encode($post_link); ?>
            };

            // 从localStorage中获取之前记录的文章，若无则初始化为空数组
            var storageKey = 'rpt_recent_posts';
            var recentPosts = [];
            try {
                recentPosts = JSON.parse(localStorage.getItem(storageKey)) || [];
            } catch (e) {
                recentPosts = [];
            }

            // 移除已存在的相同记录（避免重复）
            recentPosts = recentPosts.filter(function(post){
                return post.id !== currentPost.id;
            });
            // 将当前文章加入列表最前端
            recentPosts.unshift(currentPost);
            // 限制显示最新5篇文章
            recentPosts = recentPosts.slice(0, 5);
            // 更新localStorage
            localStorage.setItem(storageKey, JSON.stringify(recentPosts));

            // 渲染浮层中的文章列表
            var listEl = document.getElementById('rpt-post-list');
            if (listEl) {
                // 清空列表（页面刷新可能存在重复渲染问题）
                listEl.innerHTML = '';
                recentPosts.forEach(function(post){
                    var li = document.createElement('li');
                    li.style.marginBottom = "5px";
                    var a = document.createElement('a');
                    a.href = post.link;
                    a.textContent = post.title;
                    a.style.textDecoration = 'none';
                    a.style.color = '#0073aa';
                    li.appendChild(a);
                    listEl.appendChild(li);
                });
            }
        })();
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'rpt_add_floating_panel' );
