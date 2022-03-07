using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace ShoebotsLoader.License
{
    public struct AuthConfig
    {
        public string ApiUrl;
        public string PinnedCertificate;
        public string HwidSalt;

        public AuthConfig(string a, string p, string s)
        {
            ApiUrl = a;
            PinnedCertificate = p;
            HwidSalt = s;
        }
    }

    public struct ProductConfig
    {
        public string Name;
        public string PublicKey;

        public ProductConfig(string n, string k)
        {
            Name = n;
            PublicKey = k;
        }
    }
}
