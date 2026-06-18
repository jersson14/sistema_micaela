# 📜 Certificados Digitales

Esta carpeta debe contener tu certificado digital para la firma electrónica de comprobantes SUNAT.

## 📋 Instrucciones

1. **Obtén tu certificado digital** de una entidad certificadora autorizada por SUNAT
2. **Convierte el certificado a formato .pem** si es necesario
3. **Coloca el archivo aquí** con el nombre `certificado.pem`

## 🔐 Seguridad

⚠️ **IMPORTANTE**: 
- Los archivos `.pem`, `.pfx` y `.p12` están en `.gitignore`
- **NUNCA** subas tu certificado digital a GitHub
- Mantén tu certificado en un lugar seguro
- En producción, configura permisos restrictivos (chmod 600)

## 📝 Formato esperado

```
greenter/certificados/certificado.pem
```

## 🔄 Conversión de formatos

Si tienes un certificado `.pfx` o `.p12`, conviértelo a `.pem`:

```bash
# Extraer clave privada
openssl pkcs12 -in certificado.pfx -nocerts -out private.key -nodes

# Extraer certificado
openssl pkcs12 -in certificado.pfx -clcerts -nokeys -out certificate.crt

# Combinar en un solo archivo .pem
cat certificate.crt private.key > certificado.pem
```

## ✅ Verificación

Para verificar que tu certificado es válido:

```bash
openssl x509 -in certificado.pem -text -noout
```
