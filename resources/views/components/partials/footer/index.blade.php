<footer class="footer sm:footer-horizontal footer-center bg-base-100 text-base-content p-4 border-t border-base-300 flex justify-between items-center flex-col md:flex-row">
    <aside>
        <p>Copyright © {{now()->avoidMutation()->timezone(Auth::user()?->timezone ?? 'Europe/Rome')->year}} - All right reserved by fantapronostico.com</p>
    </aside>
    <aside class="grid grid-cols-2 grid-rows-2 md:flex justify-center items-center gap-x-2 gap-y-1 md:gap-x-4 lg:pr-20">
        <a href="{{ route('impressum') }}" class="link link-hover text-sm">Note Legali</a>
        <a href="{{ route('tec') }}" class="link link-hover text-sm">Termini e Condizioni</a>
        <a href="https://www.iubenda.com/privacy-policy/15649248" class="iubenda-white iubenda-noiframe iubenda-embed" title="Privacy Policy ">Privacy Policy</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src="https://cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script>
        <a href="https://www.iubenda.com/privacy-policy/15649248/cookie-policy" class="iubenda-white iubenda-noiframe iubenda-embed" title="Cookie Policy ">Cookie Policy</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src="https://cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script>
    </aside>
</footer>
