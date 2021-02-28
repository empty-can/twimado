using System;
using System.Runtime.InteropServices;  // for DllImport, Marshal
using System.Windows.Forms;

public class Win32SendKey {
  // マウス関連のWin32API
  [DllImport("user32.dll")]
  extern static uint SendInput(
    uint       nInputs,   // INPUT 構造体の数(イベント数)
    INPUT[]    pInputs,   // INPUT 構造体
    int        cbSize     // INPUT 構造体のサイズ
  );
  [StructLayout(LayoutKind.Sequential)]
  struct INPUT
  { 
    public int        type; // 0 = INPUT_MOUSE(デフォルト),
                            // 1 = INPUT_KEYBOARD
    public MOUSEINPUT mi;
  }
  [StructLayout(LayoutKind.Sequential)]
  struct MOUSEINPUT
  {
    public int    dx ;
    public int    dy ;
    public int    mouseData ;  // amount of wheel movement
    public int    dwFlags;
    public int    time;        // time stamp for the event
    public IntPtr dwExtraInfo;
  }
  const int MOUSEEVENTF_MOVED      = 0x0001 ;
  const int MOUSEEVENTF_LEFTDOWN   = 0x0002 ;  // 左ボタン Down
  const int MOUSEEVENTF_LEFTUP     = 0x0004 ;  // 左ボタン Up
  const int MOUSEEVENTF_RIGHTDOWN  = 0x0008 ;  // 右ボタン Down
  const int MOUSEEVENTF_RIGHTUP    = 0x0010 ;  // 右ボタン Up
  const int MOUSEEVENTF_MIDDLEDOWN = 0x0020 ;  // 中ボタン Down
  const int MOUSEEVENTF_MIDDLEUP   = 0x0040 ;  // 中ボタン Up
  const int MOUSEEVENTF_WHEEL      = 0x0080 ;
  const int MOUSEEVENTF_XDOWN      = 0x0100 ;
  const int MOUSEEVENTF_XUP        = 0x0200 ;
  const int MOUSEEVENTF_ABSOLUTE   = 0x8000 ;

  const int screen_length = 0x10000 ;  // for MOUSEEVENTF_ABSOLUTE (この値は固定)

  const int WINDOW_ABSOLUTE_RESOLUTION    = 65535 ;
  const int WINDOW_RESOLUTION_X    = 1920 ;
  const int WINDOW_RESOLUTION_Y    = 1080 ;

  // WindowActivate関連のWin32API
  [System.Runtime.InteropServices.DllImport(
    "user32.dll", CharSet = System.Runtime.InteropServices.CharSet.Auto)]
  static extern IntPtr FindWindow(
    string lpClassName,
    string lpWindowName);

  [System.Runtime.InteropServices.DllImport("user32.dll")]
  static extern bool SetForegroundWindow(IntPtr hWnd);

  // PowerShellから呼び出されるメソッド
  public static void LeftClick(int x, int y) { // 指定した座標を左クリックするメソッド
    INPUT[] input = new INPUT[3];
    // MOUSEEVENTF_ABSOLUTEの場合、画面サイズは 65535 で考えるので
    // 自分の解像度に合わせて修正すること(この場合 1024*768)
    // マウスに対する一連の動作の配列。1回目は移動。2回目は左ボタン押下。3回目は左ボタン開放。
    input[0].mi.dx = x * (WINDOW_ABSOLUTE_RESOLUTION / WINDOW_RESOLUTION_X);
    input[0].mi.dy = y * (WINDOW_ABSOLUTE_RESOLUTION / WINDOW_RESOLUTION_Y);
    input[0].mi.dwFlags = MOUSEEVENTF_MOVED | MOUSEEVENTF_ABSOLUTE;
    input[1].mi.dwFlags = MOUSEEVENTF_LEFTDOWN;
    input[2].mi.dwFlags = MOUSEEVENTF_LEFTUP;
    SendInput(3, input, Marshal.SizeOf(input[0]));
  }
  
  public static bool ActivateWindow(string winTitle) { // 指定したWindowをアクティブにするメソッド
    IntPtr hWnd = FindWindow(null, winTitle);
    if (hWnd != IntPtr.Zero) {
      SetForegroundWindow(hWnd);
      return true;
    }
    else {
      return false;
    }
  }

  public static void SendKey(string key) { // 指定したキーを送信するメソッド
    SendKeys.SendWait(key);
  }
}