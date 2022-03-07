using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using ShoebotsLoader.Crypto;

namespace ShoebotsLoader.License
{
    [System.Reflection.Obfuscation(Exclude = false, Feature = "+koi")]

    public static class Parser
    {
        public static string Key(byte[] license)
        {
            var entryBytes = FindEntry(license, 1);
            return entryBytes == null ? null : Encoding.UTF8.GetString(entryBytes);
        }

        public static string CustomerName(byte[] license)
        {
            var entryBytes = FindEntry(license, 2);
            return entryBytes == null ? null : Encoding.UTF8.GetString(entryBytes);
        }

        public static string CustomerEmail(byte[] license)
        {
            var entryBytes = FindEntry(license, 3);
            return entryBytes == null ? null : Encoding.UTF8.GetString(entryBytes);
        }

        public static string HardwareId(byte[] license)
        {
            var entryBytes = FindEntry(license, 4);
            return entryBytes == null ? null : Encoding.UTF8.GetString(entryBytes);
        }

        public static string ProductName(byte[] license)
        {
            var entryBytes = FindEntry(license, 7);
            return entryBytes == null ? null : Encoding.UTF8.GetString(entryBytes);
        }

        public static bool ValidateCrc(byte[] license)
        {
            var entryBytes = FindEntry(license, 255);

            var licenseDataLength = 13;
            while (licenseDataLength < license.Length)
            {
                if (license[licenseDataLength] == 1 || license[licenseDataLength] == 2 ||
                    license[licenseDataLength] == 3 ||
                    license[licenseDataLength] == 4 || license[licenseDataLength] == 7)
                {
                    if (licenseDataLength + 1 >= license.Length)
                        break;

                    licenseDataLength += license[licenseDataLength + 1] + 1;
                    continue;
                }

                if (license[licenseDataLength] == 5 || license[licenseDataLength] == 6)
                {
                    if (licenseDataLength + 5 >= license.Length)
                        break;

                    licenseDataLength += 5;
                    continue;
                }

                if (license[licenseDataLength] == 255)
                    break;

                licenseDataLength++;
            }

            var licenseData = new byte[licenseDataLength];
            Array.Copy(license, 0, licenseData, 0, licenseDataLength);

            var licenseHash = Sha1.Hash(licenseData);

            if (entryBytes[0] != licenseHash[3])
                return false;

            if (entryBytes[1] != licenseHash[2])
                return false;

            if (entryBytes[2] != licenseHash[1])
                return false;

            return entryBytes[3] == licenseHash[0];
        }

        private static byte[] FindEntry(byte[] license, int index)
        {
            //13 = padding size, adjust if we add more padding
            for (var i = 13; i < license.Length; i++)
            {
                if ((license[i] == 1 || license[i] == 2 || license[i] == 3 || license[i] == 4 || license[i] == 7) &&
                    license[i] != index)
                {
                    if (i + 1 >= license.Length)
                        return null;

                    i += license[i + 1];
                    continue;
                }

                if ((license[i] == 5 || license[i] == 6) && license[i] != index)
                {
                    if (i + 5 >= license.Length)
                        return null;

                    i += 4;
                    continue;
                }

                if (license[i] == 255 && license[i] != index)
                {
                    if (i + 5 >= license.Length)
                        return null;

                    i += 4;
                    continue;
                }

                if (license[i] != index)
                    continue;

                switch (index)
                {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 7:
                    {
                        var entryBytes = new byte[license[i + 1]];
                        Array.Copy(license, i + 2, entryBytes, 0, license[i + 1]);

                        return entryBytes;
                    }
                    case 5:
                    case 6:
                    {
                        var entryBytes = new byte[4];
                        Array.Copy(license, i + 1, entryBytes, 0, 4);

                        return entryBytes;
                    }
                    case 255:
                    {
                        var entryBytes = new byte[4];
                        Array.Copy(license, i + 1, entryBytes, 0, 4);

                        return entryBytes;
                    }
                }
            }

            return null;
        }
    }
}
