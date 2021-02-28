# Win32API をいじるC#クラス
# C#のソースを変数に格納
# $source = Get-Content
 Win32SendKey.cs | Out-String

# 規定では.NET FrameworkのSystem.Windows.Formsアセンブリ読み込まれないので追加する
# Add-Type -Language CSharp -TypeDefinition $source -ReferencedAssemblies System.Windows.Forms 
[Win32SendKey]::LeftClick(114, 321)               # (500, 600)の座標にあるダイアログのOKボタンをクリック

[System.Windows.Forms.SendKeys]::SendWait("^a")
[System.Windows.Forms.SendKeys]::SendWait("^c")
[System.Windows.Forms.SendKeys]::SendWait("^x")

Get-Clipboard -Format Text

# [System.Windows.Forms.Cursor]::Position.X # マウスのX座標
# [System.Windows.Forms.Cursor]::Position.Y # マウスのY座標
# 114
# 321
