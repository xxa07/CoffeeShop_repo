# كيف ترفعين هذا المجلد على GitHub

## الطريقة الأسهل — من الموقع مباشرة (بدون terminal)

1. روحي على https://github.com وسجلي دخول (لو ما عندك حساب، أنشئي حساب)
2. اضغطي زر **+** فوق يمين → **New repository**
3. اسم الـ repo: `SaudiCoffeeShop` (أو أي اسم تبينه)
4. الوصف: `CYS 538 Project - Weak Session IDs vulnerability demo`
5. اختاري **Public** (المتطلبات تطلب رابط GitHub، فاجعليه عام)
6. **لا** تحطي علامة على "Add a README" ولا "Add .gitignore" — عندنا هم مسبقاً
7. اضغطي **Create repository**
8. في الصفحة اللي تطلع، تشوفين قسم اسمه "uploading an existing file" — اضغطي على الرابط
9. اسحبي **كل الملفات** اللي في مجلد `SaudiCoffeeShop_repo/` ولصقيها هنا (اختاري الكل واسحبيهم)
10. تحت، في خانة Commit changes اكتبي: `Initial commit: project code and screenshots`
11. اضغطي **Commit changes**

تم! الرابط بيكون: `https://github.com/<username>/SaudiCoffeeShop`

---

## الطريقة بالـ Terminal (لو تفضّلين)

افتحي Terminal على الماك وروحي للمجلد:

```bash
cd ~/Downloads/SaudiCoffeeShop_repo
```

(أو وين ما كان موقع المجلد)

ثم:

```bash
git init -b main
git add .
git commit -m "Initial commit: SaudiCoffeeShop CYS 538 project"
```

روحي على github.com وأنشئي repo فاضي (نفس الخطوات 1-7 فوق). بعدها GitHub يعطيك أوامر — انسخيها وألصقيها في Terminal. شكل الأوامر:

```bash
git remote add origin https://github.com/<username>/SaudiCoffeeShop.git
git branch -M main
git push -u origin main
```

أول مرة بيطلب اسم المستخدم وكلمة سر. لو حسابك فيه 2FA، تستخدمين Personal Access Token بدل الباسورد ([شرحه هنا](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens)).

---

## بعد ما ترفعين

1. انسخي رابط الـ repo (مثل: `https://github.com/<username>/SaudiCoffeeShop`)
2. الصقيه في الـ Appendix A في التقرير (في مكان `<INSERT GITHUB REPOSITORY LINK HERE>`)
3. تأكدي إن الـ repo **Public** عشان الدكتور يقدر يفتحه

---

## تحديثات لاحقاً (لما تجي الصور من سوسو أو ينضاف التقرير)

من الـ Terminal:

```bash
cd ~/Downloads/SaudiCoffeeShop_repo

# انسخي الصور الجديدة لـ screenshots/figures/
# انسخي التقرير النهائي لـ report/

git add .
git commit -m "Add hardened-build screenshots and final report"
git push
```

أو من الموقع: روحي على الـ repo → **Add file** → **Upload files** → اسحبي الجديد.

---

## لو ما تبين تستخدمين GitHub أصلاً

المتطلبات تطلب **GitHub repository link in the appendices**. لو فعلاً ما تبين، البديل الوحيد هو GitLab أو Bitbucket (نفس الفكرة).
