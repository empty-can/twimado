Add-Type -AssemblyName System.Windows.Forms

[System.Windows.Forms.Cursor]::Position.X # �}�E�X��X���W
[System.Windows.Forms.Cursor]::Position.Y # �}�E�X��Y���W
# 110
# 94
# exit

# Win32API ��������C#�N���X
# C#�̃\�[�X��ϐ��Ɋi�[
$source = Get-Content Win32SendKey.cs | Out-String

# �K��ł�.NET Framework��System.Windows.Forms�A�Z���u���ǂݍ��܂�Ȃ��̂Œǉ�����
Add-Type -Language CSharp -TypeDefinition $source -ReferencedAssemblies System.Windows.Forms 

[Win32SendKey]::LeftClick(120, 15)  # test.php�̃^�u���N���b�N

[System.Windows.Forms.SendKeys]::SendWait("{F5}")

Start-Sleep -s 1

[Win32SendKey]::LeftClick(600, 600)  # test.php�̃y�[�W���N���b�N

[System.Windows.Forms.SendKeys]::SendWait("^a")
[System.Windows.Forms.SendKeys]::SendWait("^c")

Start-Sleep -s 1

[Win32SendKey]::LeftClick(356, 15)  # ���[�����g�ҏW�y�[�W�̃^�u���N���b�N
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1163, 131) # ���[�����g�ҏW�y�[�W���J��
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1073, 183) # ���[�����g�ҏW��I��
Start-Sleep -s 3
[Win32SendKey]::LeftClick(150, 400) # �|�b�v�A�b�v������
Start-Sleep -s 3
[System.Windows.Forms.SendKeys]::SendWait("^f")
[System.Windows.Forms.SendKeys]::SendWait("{ENTER}")
Start-Sleep -s 3
[Win32SendKey]::LeftClick(1200, 580) # �����N������N���b�N
Start-Sleep -s 1
[System.Windows.Forms.SendKeys]::SendWait("{END}")
Start-Sleep -s 1

$ids = Get-Clipboard -Format Text 
$ids -split "\r\n" | ForEach-Object -Process {
    Set-Clipboard $_
    [Win32SendKey]::LeftClick(900, 860)  # ���[�����g�ҏW�y�[�W�̓��̓o�[���N���b�N
    [System.Windows.Forms.SendKeys]::SendWait("^a")
    [System.Windows.Forms.SendKeys]::SendWait("{DEL}")
    [System.Windows.Forms.SendKeys]::SendWait("^v")
    Start-Sleep -s 1
    [Win32SendKey]::LeftClick(1230, 860)  # �c�C�[�g��o�^
    Start-Sleep -s 1
    [System.Windows.Forms.SendKeys]::SendWait("^{END}")
    Start-Sleep -s 1
}

[Win32SendKey]::LeftClick(1514, 126)  # �����{�^���������Ċ���
Start-Sleep -s 2
# [Win32SendKey]::LeftClick(878, 136)  # ��Ń{�^���������Ċ���
# Start-Sleep -s 2

[Win32SendKey]::LeftClick(530, 15)    # �g���@�\�̃^�u���N���b�N
Start-Sleep -s 2
[Win32SendKey]::LeftClick(1122, 421)   # �g���@�\��L���ɂ���
Start-Sleep -s 2

[Win32SendKey]::LeftClick(356, 15)  # ���[�����g�ҏW�y�[�W�̃^�u���N���b�N
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1163, 131) # ���[�����g�ҏW�y�[�W���J��
Start-Sleep -s 1
[Win32SendKey]::LeftClick(1073, 183) # ���[�����g�ҏW��I��

Start-Sleep -s 5

[Win32SendKey]::LeftClick(530, 15)    # �g���@�\�̃^�u���N���b�N
Start-Sleep -s 2
[Win32SendKey]::LeftClick(1122, 421)   # �g���@�\�𖳌��ɂ���
Start-Sleep -s 2

Start-Sleep -s 1800	# ���T�C�Y���I���̂�҂�

[Win32SendKey]::LeftClick(356, 15)  # ���[�����g�ҏW�y�[�W�̃^�u���N���b�N
Start-Sleep -s 1

exit