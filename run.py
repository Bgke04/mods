import os,time


cm = input("Install Modul/pip jika sudah ada pilih n[y/n]")
if cm == 'y':
	print("Ini butuh waktu cukup lama ")
	time.sleep(3)
	os.system("pip install fbthon")
	os.system("pip install requests")
	os.system("pip install fbthon")
	os.system("pip install rich")
	os.system("pip install bs4")
	os.system("pip install pyfiglet")
	os.system("pip install socket")
	print("Instalasi modul selesai")
	time.sleep(3)
	os.system("python3 code.py")
	
else:
	os.system("python3 code.py")
		
