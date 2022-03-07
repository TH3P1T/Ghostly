using System.Management;
using ShoebotsLoader.Crypto;

namespace ShoebotsLoader.License
{
    [System.Reflection.Obfuscation(Exclude = false, Feature = "+koi")]

    public static class Hwid
    {
        public static string Generate(string salt = "gH0sT1y")
        {
            return Sha1.HashToString(salt + ProcessorId() + MotherboardSerial() + SystemDriveSerial());
        }

        private static string ProcessorId()
        {
            var managementObjectSearcher = new ManagementObjectSearcher("SELECT * FROM Win32_Processor");
            var managementObjects = managementObjectSearcher.Get();
            var processorIds = "";
            foreach (var managementObject in managementObjects)
            {
                if (managementObject["ProcessorId"] != null)
                    processorIds += managementObject["ProcessorId"].ToString();
            }

            return processorIds;
        }

        private static string MotherboardSerial()
        {
            var managementObjectSearcher = new ManagementObjectSearcher("SELECT * FROM Win32_BaseBoard");
            var managementObjects = managementObjectSearcher.Get();
            var motherboardSerial = "";
            foreach (var managementObject in managementObjects)
            {
                if (managementObject["SerialNumber"] != null)
                    motherboardSerial += managementObject["SerialNumber"].ToString();
            }

            return motherboardSerial;
        }

        private static string SystemDriveSerial()
        {
            var managementObjectSearcher = new ManagementObjectSearcher("SELECT * FROM Win32_DiskDrive WHERE DeviceID LIKE \"%PHYSICALDRIVE0\"");
            var managementObjects = managementObjectSearcher.Get();
            foreach (var managementObject in managementObjects)
            {
                if (managementObject["SerialNumber"] != null)
                    return managementObject["SerialNumber"].ToString();
            }

            managementObjectSearcher = new ManagementObjectSearcher("SELECT * FROM Win32_DiskDrive WHERE DeviceID LIKE \"%PHYSICALDRIVE1\"");
            managementObjects = managementObjectSearcher.Get();
            foreach (var managementObject in managementObjects)
            {
                if (managementObject["SerialNumber"] != null)
                    return managementObject["SerialNumber"].ToString();
            }

            managementObjectSearcher = new ManagementObjectSearcher("SELECT * FROM Win32_DiskDrive WHERE DeviceID LIKE \"%PHYSICALDRIVE2\"");
            managementObjects = managementObjectSearcher.Get();
            foreach (var managementObject in managementObjects)
            {
                if (managementObject["SerialNumber"] != null)
                    return managementObject["SerialNumber"].ToString();
            }

            managementObjectSearcher = new ManagementObjectSearcher("SELECT * FROM Win32_DiskDrive WHERE DeviceID LIKE \"%PHYSICALDRIVE3\"");
            managementObjects = managementObjectSearcher.Get();
            foreach (var managementObject in managementObjects)
            {
                if (managementObject["SerialNumber"] != null)
                    return managementObject["SerialNumber"].ToString();
            }

            return "DS1234567890";
        }
    }
}
