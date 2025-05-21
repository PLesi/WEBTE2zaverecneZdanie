package com.example.webte2.PDFController;
import com.itextpdf.text.*;
import com.itextpdf.text.log.Logger;
import com.itextpdf.text.log.LoggerFactory;
import com.itextpdf.text.pdf.*;
import com.openhtmltopdf.pdfboxout.PdfRendererBuilder;
import jakarta.servlet.http.HttpServletRequest;
import org.apache.tomcat.util.http.fileupload.IOUtils;
import org.jsoup.Jsoup;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.core.io.ByteArrayResource;
import org.springframework.http.*;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;
import org.apache.commons.compress.archivers.zip.ZipArchiveEntry;
import org.apache.commons.compress.archivers.zip.ZipArchiveOutputStream;
import org.apache.commons.io.FileUtils;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;

import com.itextpdf.text.Document;
import com.itextpdf.text.pdf.PdfCopy;
import com.itextpdf.text.pdf.PdfReader;
import com.itextpdf.text.DocumentException;

import java.io.*;
import java.nio.file.Files;
import java.time.LocalDateTime;
import java.util.*;
import java.util.List;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.media.Content;
import io.swagger.v3.oas.annotations.media.Schema;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import java.io.*;
import java.util.ArrayList;
import java.util.Arrays;

import javax.print.Doc;

@RestController
@RequestMapping("/api/pdf")
public class PDFController {
    private static final Logger logger = LoggerFactory.getLogger(PDFController.class);

    private String getClientIp(HttpServletRequest request) {
        String xfHeader = request.getHeader("X-Forwarded-For");
        if (xfHeader == null) {
            return request.getRemoteAddr();
        }
        return xfHeader.split(",")[0]; // First IP in the list
    }
    private boolean isLocalhost(String ip) {
        return "127.0.0.1".equals(ip) || "::1".equals(ip) || "0:0:0:0:0:0:0:1".equals(ip);
    }
    @Autowired
    private ApiKeyRepository apiKeyRepo;

    @Autowired
    private HistoryRepository historyRepo;

    @Autowired
    private LocationService locationService;
    @Operation(
            summary = "Compress a PDF file",
            description = "Compresses a PDF file with the specified compression level."
    )
    @ApiResponses(value = {
            @ApiResponse(
                    responseCode = "200",
                    description = "PDF compressed successfully",
                    content = @Content(mediaType = "application/pdf")
            ),
            @ApiResponse(
                    responseCode = "400",
                    description = "Invalid input",
                    content = @Content(mediaType = "application/json")
            ),
            @ApiResponse(
                    responseCode = "401",
                    description = "Unauthorized",
                    content = @Content(mediaType = "application/json")
            )
    })
        @PostMapping("/compressNew")
        public ResponseEntity<?> compressPdfNew(
                @RequestParam("file") MultipartFile file,
                @RequestParam("level") int compressionLevel,
                @RequestParam("apiKey") String apiKey,
                @RequestParam(value = "platform", defaultValue = "api") String platform,
                HttpServletRequest request

        ) throws IOException, DocumentException {

            ApiKey key = apiKeyRepo.findByApiKey(apiKey);
            if (key == null) {
                return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
            }

            Long userId = key.getUserId();
            // Validate file
            if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
                return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
            }

            // Validate compression level
            if (compressionLevel < 0 || compressionLevel > 9) {
                return ResponseEntity.badRequest().body("Compression level must be between 0 and 9.");
            }

            // Read input PDF using OpenPDF
            com.lowagie.text.pdf.PdfReader reader = new com.lowagie.text.pdf.PdfReader(file.getInputStream());
            ByteArrayOutputStream compressedOutput = new ByteArrayOutputStream();

            // Create stamper and compress
            com.lowagie.text.pdf.PdfStamper stamper = new com.lowagie.text.pdf.PdfStamper(reader, compressedOutput);
            stamper.setFullCompression(); // Enables object streams (PDF 1.5)
            stamper.getWriter().setCompressionLevel(compressionLevel); // 0–9
            stamper.close();
            reader.close();

            // Return compressed PDF as download
            byte[] compressedBytes = compressedOutput.toByteArray();
            ByteArrayResource resource = new ByteArrayResource(compressedBytes);
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("compressNew");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);
            return ResponseEntity.ok()
                    .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=compressed.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(compressedBytes.length)
                    .body(resource);
        }

        //ovela komplikovanejsia a ani toto nefunguje 100%ne
//    private ResponseEntity<?> validateApiKey(String apiKey) {
//        if (apiKey == null || !apiKeyRepo.existsByAp(apiKey)) {
//            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
//        }
//        return null;
//    }

//    @PostMapping("/compress")
//    public ResponseEntity<?> compressPdf(
//            @RequestParam("file") MultipartFile file,
//            @RequestParam("level") int compressionLevel,
//            @RequestParam("apiKey") String apiKey,
//            @RequestParam(value = "platform", defaultValue = "api") String platform,
//            HttpServletRequest request
//    ) {
//        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
//        if (key == null) {
//            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
//        }
//
//        Long userId = key.getUserId();
//
//        if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
//            return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
//        }
//
//        if (compressionLevel < 1 || compressionLevel > 4) {
//            return ResponseEntity.badRequest().body("Compression level must be between 1 and 4.");
//        }
//
//        // Map your custom levels to Ghostscript settings
//        String gsQuality = switch (compressionLevel) {
//            case 1 -> "/screen";
//            case 2 -> "/ebook";
//            case 3 -> "/printer";
//            case 4 -> "/prepress";
//            default -> "/screen";
//        };
//
//        try {
//            // Save input file to a temp file
//            File inputTemp = File.createTempFile("input-", ".pdf");
//            file.transferTo(inputTemp);
//
//            // Create temp file for output
//            File outputTemp = File.createTempFile("output-", ".pdf");
//
//            // Build Ghostscript command
//            ProcessBuilder pb = new ProcessBuilder(
//                    "C:/Program Files/gs/gs10.05.1/bin/gswin64c.exe",
//                    "-sDEVICE=pdfwrite",
//                    "-dCompatibilityLevel=1.4",
//                    "-dNOPAUSE",
//                    "-dQUIET",
//                    "-dBATCH",
//                    "-dPDFSETTINGS=" + gsQuality,
//                    "-sOutputFile=" + outputTemp.getAbsolutePath(),
//                    inputTemp.getAbsolutePath()
//            );
//
//            Process process = pb.start();
//            int exitCode = process.waitFor();
//            if (exitCode != 0) {
//                return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
//                        .body("Ghostscript failed with exit code: " + exitCode);
//            }
//
//            // Read compressed file
//            byte[] compressedBytes = Files.readAllBytes(outputTemp.toPath());
//            ByteArrayResource resource = new ByteArrayResource(compressedBytes);
//
//            // Optional: log client location
//            String ip = getClientIp(request);
//            if (isLocalhost(ip)) ip = "8.8.8.8";
//            Location location = locationService.getLocationFromIp(ip);
//
//            History history = new History();
//            history.setUser_id(userId);
//            history.setOperation("compress");
//            history.setTime(LocalDateTime.now());
//            history.setCity(location.getCity());
//            history.setState(location.getState());
//            history.setPlatform(platform);
//            historyRepo.save(history);
//
//            // Clean up
//            inputTemp.delete();
//            outputTemp.delete();
//
//            return ResponseEntity.ok()
//                    .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=compressed.pdf")
//                    .contentType(MediaType.APPLICATION_PDF)
//                    .contentLength(compressedBytes.length)
//                    .body(resource);
//
//        } catch (IOException | InterruptedException e) {
//            e.printStackTrace();
//            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
//                    .body("Error during compression: " + e.getMessage());
//        }
//    }
@Operation(
        summary = "Compress a PDF file",
        description = "Compresses a PDF file with the specified compression level."
)
@ApiResponses(value = {
        @ApiResponse(
                responseCode = "200",
                description = "PDF compressed successfully",
                content = @Content(mediaType = "application/pdf")
        ),
        @ApiResponse(
                responseCode = "400",
                description = "Invalid input",
                content = @Content(mediaType = "application/json")
        ),
        @ApiResponse(
                responseCode = "401",
                description = "Unauthorized",
                content = @Content(mediaType = "application/json")
        )
})
    @PostMapping("/compress")
    public ResponseEntity<?> compressPdf(
            @Parameter(description = "The PDF file to compress", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "Compression level (0-9)", required = true)
            @RequestParam("level") int compressionLevel,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform
            , HttpServletRequest request
    ) {


        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId(); // Get user from ApiKey

        try {
            if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
                return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
            }

            if (compressionLevel < 0 || compressionLevel > 9) {
                return ResponseEntity.badRequest().body("Compression level must be between 0 and 9.");
            }

            PdfReader reader = new PdfReader(file.getInputStream());
            ByteArrayOutputStream compressedOutput = new ByteArrayOutputStream();
            PdfStamper stamper = new PdfStamper(reader, compressedOutput);
            stamper.setFullCompression();
            stamper.getWriter().setCompressionLevel(compressionLevel);
            stamper.close();
            reader.close();

            byte[] compressedBytes = compressedOutput.toByteArray();
            ByteArrayResource resource = new ByteArrayResource(compressedBytes);

            String ip = getClientIp(request);
            System.out.println("Client IP: " + ip);
            if (isLocalhost(ip)) {
                ip = "8.8.8.8"; // Google's public DNS IP to simulate real location
            }
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("compress");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);


            return ResponseEntity.ok()
                    .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=compressed.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(compressedBytes.length)
                    .body(resource);

        } catch (IOException | DocumentException e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error processing PDF: " + e.getMessage());
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Unexpected server error: " + e.getMessage());
        }
    }
    @Operation(
            summary = "Convert a JPG to PDF",
            description = "Convert a JPG file to PDF file."
    )
    @ApiResponses(value = {
            @ApiResponse(
                    responseCode = "200",
                    description = "JPG converted to PDF  successfully",
                    content = @Content(mediaType = "application/pdf")
            ),
            @ApiResponse(
                    responseCode = "400",
                    description = "Invalid input",
                    content = @Content(mediaType = "application/json")
            ),
            @ApiResponse(
                    responseCode = "401",
                    description = "Unauthorized",
                    content = @Content(mediaType = "application/json")
            )
    })
    @PostMapping("/jpgToPdf")
    public ResponseEntity<?> convertJpgToPdf(
            @Parameter(description = "The JPG file to convert", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) throws IOException,DocumentException {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (file.isEmpty()|| !file.getOriginalFilename().toLowerCase().endsWith(".jpg"))
            return ResponseEntity.badRequest().body("Invalid input");
        try {
            ByteArrayOutputStream pdfOutputStream  = new ByteArrayOutputStream();
            Image image = Image.getInstance(file.getBytes());
            Document document = new Document();
            PdfWriter.getInstance(document, pdfOutputStream );
            document.open();
            image.scaleToFit(PageSize.A4.getWidth(), PageSize.A4.getHeight());
            image.setAlignment(Image.ALIGN_CENTER);
            document.add(image);
            document.close();
            byte[] pdfBytes = pdfOutputStream.toByteArray();
            ByteArrayResource resource = new ByteArrayResource(pdfBytes);
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("jpgToPdf");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);
            return ResponseEntity.ok().header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=jpgtopdf.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(resource.contentLength())
                    .body(resource);
        }
        catch (Exception e)
        {return ResponseEntity.badRequest().body("Something went wrong");}

    }

    @Operation(summary = "Merge two PDF files", description = "Merges two PDF files into one.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "PDFs merged successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/merge")
    public ResponseEntity<?> mergePdfs(
            @Parameter(description = "The first PDF file to merge", required = true)
            @RequestParam("file1") MultipartFile file1,

            @Parameter(description = "The second PDF file to merge", required = true)
            @RequestParam("file2") MultipartFile file2,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) throws  IOException,DocumentException{
        try {
            ApiKey key = apiKeyRepo.findByApiKey(apiKey);
            if (key == null) {
                return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
            }

            Long userId = key.getUserId();
            if (file1.isEmpty() || file2.isEmpty() || !file1.getOriginalFilename().toLowerCase().endsWith(".pdf") || !file2.getOriginalFilename().toLowerCase().endsWith(".pdf"))
                return ResponseEntity.badRequest().body("Invalid input");
            try {
                ByteArrayOutputStream pdfOutput = new ByteArrayOutputStream();
                PdfReader pdfReader = new PdfReader(file1.getInputStream());
                Document document = new Document();
                PdfCopy pdfCopy = new PdfCopy(document, pdfOutput);
                document.open();
                PdfReader pdfReader2 = new PdfReader(file2.getInputStream());
                int pages1 = pdfReader.getNumberOfPages();
                int pages2 = pdfReader2.getNumberOfPages();
                for (int i = 1; i <= pages1; i++) {
                    PdfImportedPage page = pdfCopy.getImportedPage(pdfReader, i);
                    pdfCopy.addPage(page);
                }
                pdfReader.close();
                for (int i = 1; i <= pages2; i++) {
                    PdfImportedPage page = pdfCopy.getImportedPage(pdfReader2, i);
                    pdfCopy.addPage(page);
                }
                pdfReader.close();
                document.close();
                byte[] mergedPdf = pdfOutput.toByteArray();
                ByteArrayResource resource = new ByteArrayResource(mergedPdf);
                String ip = getClientIp(request);
                Location location = locationService.getLocationFromIp(ip);
                String city = location.getCity();
                String state = location.getState();

                History history = new History();
                history.setUser_id(userId); // Use actual user ID
                history.setOperation("merge");
                history.setTime(LocalDateTime.now());
                history.setCity(city);
                history.setState(state);
                history.setPlatform(platform);
                historyRepo.save(history);
                return ResponseEntity.ok().header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=merged.pdf")
                        .contentType(MediaType.APPLICATION_PDF)
                        .contentLength(resource.contentLength())
                        .body(resource);
            } catch (Exception e) {
                return ResponseEntity.badRequest().body("Something went wrong" + e.getMessage());
            }
        }
        catch (Exception e) {
            logger.error("Error merging PDFs", e);
            StringWriter sw = new StringWriter();
            e.printStackTrace(new PrintWriter(sw));
            String exceptionAsString = sw.toString();

            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error occurred:\n" + exceptionAsString);
        }
}
    @Operation(summary = "Rotate PDF pages", description = "Rotates all pages in a PDF by the specified angle.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "PDF pages rotated successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/rotatePages")
    public ResponseEntity<?> rotatePages(
            @Parameter(description = "The PDF file to rotate", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "Rotation angle in degrees (e.g., 90, 180, 270)", required = true)
            @RequestParam("degree") int rotationAngle,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request)
            throws IOException, DocumentException {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
            return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
        }

        try {
            // Read the PDF
            PdfReader reader = new PdfReader(file.getInputStream());

            // Prepare output
            ByteArrayOutputStream output = new ByteArrayOutputStream();

            // Use PdfStamper to apply rotation
            PdfStamper stamper = new PdfStamper(reader, output);

            int totalPages = reader.getNumberOfPages();

            for (int i = 1; i <= totalPages; i++) {
                // Get current rotation (default is 0 if not set)
                PdfDictionary pageDict = reader.getPageN(i);
                PdfNumber rotate = pageDict.getAsNumber(PdfName.ROTATE);
                int currentRotation = rotate != null ? rotate.intValue() : 0;

                // Add new rotation
                int newRotation = (currentRotation + rotationAngle) % 360;

                // Set new rotation value
                pageDict.put(PdfName.ROTATE, new PdfNumber(newRotation));
            }

            stamper.close();
            reader.close();

            ByteArrayResource resource = new ByteArrayResource(output.toByteArray());
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("rotatePages");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);
            return ResponseEntity.ok()
                    .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=rotated.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(resource.contentLength())
                    .body(resource);

        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Failed to rotate pages: " + e.getMessage());
        }
    }
    @Operation(summary = "Protect a PDF file", description = "Adds password protection to a PDF file.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "PDF protected successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/protect")
    private ResponseEntity<?> protectPdf(
            @Parameter(description = "The PDF file to protect", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "Password to protect the PDF", required = true)
            @RequestParam("password") String password,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) throws  IOException,DocumentException {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
            return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
        }
        try {
            Document document = new Document();
            ByteArrayOutputStream pdfProtected = new ByteArrayOutputStream();
            PdfReader pdfReader = new PdfReader(file.getInputStream());
            PdfStamper pdfStamper = new PdfStamper(pdfReader,pdfProtected);
            pdfStamper.setEncryption(password.getBytes(),
                    password.getBytes(),
                    PdfWriter.ALLOW_PRINTING,          // allowed permissions
                    PdfWriter.STANDARD_ENCRYPTION_128  );
            pdfStamper.close();
            pdfReader.close();
            ByteArrayResource resource = new ByteArrayResource(pdfProtected.toByteArray());
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("protect");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);
            return ResponseEntity.ok().header(HttpHeaders.CONTENT_DISPOSITION,"attachment;filename=protected.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(resource.contentLength())
                    .body(resource);
        }
        catch (Exception e){
            return ResponseEntity.badRequest().body("Something went wrong" + e.getMessage());
        }
    }@Operation(summary = "Edit a PDF file", description = "Updates the content of a PDF file.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "PDF annotated successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/edit")
    //@CrossOrigin(origins = "http://127.0.0.1:5500")
    public ResponseEntity<?> annotatePdf(
            @Parameter(description = "The PDF file to edit", required = true)
            @RequestParam("pdf") MultipartFile pdfFile,

            @Parameter(description = "The annotation image to add", required = true)
            @RequestParam("annotation") MultipartFile annotationImage,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (pdfFile.isEmpty() || annotationImage.isEmpty()) {
            return ResponseEntity.badRequest().body("Missing files.");
        }

        try {
            PdfReader reader = new PdfReader(pdfFile.getInputStream());
            ByteArrayOutputStream outputStream = new ByteArrayOutputStream();
            PdfStamper stamper = new PdfStamper(reader, outputStream);

            Image annotation = Image.getInstance(annotationImage.getBytes());
            PdfContentByte canvas = stamper.getOverContent(1);

            annotation.setAbsolutePosition(0, 0); // adjust if needed
            annotation.scaleToFit(reader.getPageSize(1).getWidth(), reader.getPageSize(1).getHeight());

            canvas.addImage(annotation);

            stamper.close();
            reader.close();

            ByteArrayResource resource = new ByteArrayResource(outputStream.toByteArray());
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("edit");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);
            return ResponseEntity.ok()
                    .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=annotated.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(resource.contentLength())
                    .body(resource);
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body("Error: " + e.getMessage());
        }
    }
    @Operation(summary = "Add page numbers to a PDF", description = "Adds page numbers to a PDF file at the specified position.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "Page numbers added successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/numberPages")
    public ResponseEntity<?> pageNumber(
            @Parameter(description = "The PDF file to add page numbers to", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Position of the page numbers (e.g., topLeft, bottomRight)", required = true)
            @RequestParam(value = "position", defaultValue = "bottomRight") String position,

            @Parameter(description = "Font size for the page numbers", required = true)
            @RequestParam(value = "fontSize", defaultValue = "12") int fontSize,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) throws IOException, DocumentException {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
            return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
        }

        if (!Arrays.asList("topLeft", "topRight", "bottomLeft", "bottomRight").contains(position)) {
            return ResponseEntity.badRequest().body("Invalid position. Use topLeft, topRight, bottomLeft, or bottomRight.");
        }
        int x=550,y=20;
        switch (position){
            case "topLeft":
                x= 50;
                y= 745;
                break;
            case "topRight":
                x= 550;
                y= 745;
                break;
            case "bottomLeft":
                x= 50;
                y= 20;
                break;
            case "bottomRight":
                x= 550;
                y= 20;
                break;

        }
        try {
            ByteArrayOutputStream outputStream = new ByteArrayOutputStream();
            PdfReader reader = new PdfReader(file.getInputStream());
            PdfStamper stamper = new PdfStamper(reader, outputStream);

            // Add page numbering
            int totalPages = reader.getNumberOfPages();
            for (int i = 1; i <= totalPages; i++) {
                PdfContentByte cb = stamper.getOverContent(i);
                ColumnText.showTextAligned(cb, Element.ALIGN_CENTER,
                        new Phrase("" + i, new Font(Font.FontFamily.HELVETICA, fontSize)),
                        x, y, 0);
            }

            stamper.close();
            reader.close();

            byte[] pdfBytes = outputStream.toByteArray();
            ByteArrayResource resource = new ByteArrayResource(pdfBytes);
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("numberPages");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);
            return ResponseEntity.ok()
                    .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=numbered.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(pdfBytes.length)
                    .body(resource);

        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Something went wrong: " + e.getMessage());
        }
    }

    public static class PageNumbering extends PdfPageEventHelper {
        @Override
        public void onEndPage(PdfWriter writer,Document document){
            int pageNumber = writer.getPageNumber();
            Font font = new Font(Font.FontFamily.HELVETICA, 12, Font.NORMAL);
            Phrase pageNumberPhrase = new Phrase("Page"+ pageNumber,font);
            int pageX = (int) (document.right() - 100);
            int pageY= (int) (document.bottom()-10);
            ColumnText.showTextAligned(writer.getDirectContent(), Element.ALIGN_CENTER,
                    pageNumberPhrase, pageX, pageY, 0);
        }

    }
    @Operation(summary = "Remove a page from a PDF", description = "Removes a specific page from a PDF file.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "Page removed successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/removePage")
    public ResponseEntity<?> removePage(
            @Parameter(description = "The PDF file to remove a page from", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "The page number to remove", required = true)
            @RequestParam("page") int pageNumber,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) throws IOException,DocumentException {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith("pdf"))
        return  ResponseEntity.badRequest().body("Invalid file.Please upload a pdf");
    try {
        Document document = new Document();
        ByteArrayOutputStream pdfPageDeleted = new ByteArrayOutputStream();
        PdfCopy pdfCopy = new PdfCopy(document,pdfPageDeleted);
        PdfReader pdfReader = new PdfReader(file.getInputStream());
        document.open();
        int pages = pdfReader.getNumberOfPages();
        for (int i =1;i<=pages;i++){
            if (i==pageNumber)
                continue;
            PdfImportedPage copiedPage = pdfCopy.getImportedPage(pdfReader,i);
            pdfCopy.addPage(copiedPage);
        }
        pdfReader.close();
        pdfCopy.close();
        document.close();
        byte[] mergedPdf = pdfPageDeleted.toByteArray();
        ByteArrayResource resource = new ByteArrayResource(mergedPdf);
        String ip = getClientIp(request);
        Location location = locationService.getLocationFromIp(ip);
        String city = location.getCity();
        String state = location.getState();

        History history = new History();
        history.setUser_id(userId); // Use actual user ID
        history.setOperation("removePage");
        history.setTime(LocalDateTime.now());
        history.setCity(city);
        history.setState(state);
        history.setPlatform(platform);
        historyRepo.save(history);
        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=pagedeleted.pdf")
                .contentType(MediaType.APPLICATION_PDF)
                .contentLength(resource.contentLength())
                .body(resource);
        }
        catch (Exception e){
            return ResponseEntity.badRequest().body("Something went wrong"+ e.getMessage());
        }
    }


    @Operation(summary = "Split a PDF file", description = "Splits a PDF file into smaller parts.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "PDF split successfully", content = @Content(mediaType = "application/zip")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })

    @PostMapping("/split")
    public ResponseEntity<?> splitPdf(
            @Parameter(description = "The PDF file to split", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) throws IOException, DocumentException {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
            return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
        }

        File tempFile = File.createTempFile("uploaded-", ".pdf");
        file.transferTo(tempFile);

        PdfReader reader = new PdfReader(tempFile.getAbsolutePath());
        int totalPages = reader.getNumberOfPages();
        List<byte[]> splitFiles = new ArrayList<>();

        int groupSize = totalPages <= 5 ? 1 : 2;
        int fileIndex = 1;

        // Split the PDF
        for (int i = 1; i <= totalPages; i += groupSize) {
            ByteArrayOutputStream baos = new ByteArrayOutputStream();
            Document document = new Document();
            PdfCopy copy = new PdfCopy(document, baos);
            document.open();

            for (int j = 0; j < groupSize && (i + j) <= totalPages; j++) {
                copy.addPage(copy.getImportedPage(reader, i + j));
            }

            document.close();
            splitFiles.add(baos.toByteArray());
        }

        reader.close();
        tempFile.delete();

        // Create a Zip file containing all the split PDFs
        ByteArrayOutputStream zipBaos = new ByteArrayOutputStream();
        try (ZipArchiveOutputStream zipOut = new ZipArchiveOutputStream(zipBaos)) {
            for (int i = 0; i < splitFiles.size(); i++) {
                ByteArrayInputStream bis = new ByteArrayInputStream(splitFiles.get(i));
                ZipArchiveEntry entry = new ZipArchiveEntry("split_part_" + (i + 1) + ".pdf");
                zipOut.putArchiveEntry(entry);
                IOUtils.copy(bis, zipOut);
                zipOut.closeArchiveEntry();
            }
        }

        // Set the response headers for the zip file
        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(MediaType.APPLICATION_OCTET_STREAM);
        headers.setContentDispositionFormData("attachment", "split_pdfs.zip");
        String ip = getClientIp(request);
        Location location = locationService.getLocationFromIp(ip);
        String city = location.getCity();
        String state = location.getState();

        History history = new History();
        history.setUser_id(userId); // Use actual user ID
        history.setOperation("split");
        history.setTime(LocalDateTime.now());
        history.setCity(city);
        history.setState(state);
        history.setPlatform(platform);
        historyRepo.save(history);

        return new ResponseEntity<>(zipBaos.toByteArray(), headers, HttpStatus.OK);
    }
    @Operation(summary = "Convert a webpage to PDF", description = "Converts a webpage to a PDF file.(must be static webpage)")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "Webpage converted to PDF successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/webpage-to-pdf")
    public ResponseEntity<?> convertWebpageToPdf(
            @Parameter(description = "The URL of the webpage to convert", required = true)
            @RequestParam("url") String url,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) {
        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        try {
            // Fetch and convert HTML to XHTML
            org.jsoup.nodes.Document document = Jsoup.connect(url).get();
            document.outputSettings().syntax(org.jsoup.nodes.Document.OutputSettings.Syntax.xml);
            String xhtml = document.html();

            // Generate PDF
            ByteArrayOutputStream baos = new ByteArrayOutputStream();
            PdfRendererBuilder builder = new PdfRendererBuilder();
            builder.useFastMode();
            builder.withHtmlContent(xhtml, url); // base URI for resources
            builder.toStream(baos);
            builder.run();

            // Return PDF
            HttpHeaders headers = new HttpHeaders();
            headers.setContentType(MediaType.APPLICATION_PDF);
            headers.setContentDisposition(ContentDisposition.attachment().filename("webpage.pdf").build());
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            String city = location.getCity();
            String state = location.getState();

            History history = new History();
            history.setUser_id(userId); // Use actual user ID
            history.setOperation("webPagetoPdf");
            history.setTime(LocalDateTime.now());
            history.setCity(city);
            history.setState(state);
            history.setPlatform(platform);
            historyRepo.save(history);
            return new ResponseEntity<>(baos.toByteArray(), headers, HttpStatus.OK);

        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(("Failed to convert URL: " + e.getMessage()).getBytes());
        }
    }
    @Operation(summary = "Rearrange PDF pages", description = "Rearranges the pages of a PDF file based on the specified order.")
    @ApiResponses(value = {
            @ApiResponse(responseCode = "200", description = "PDF pages rearranged successfully", content = @Content(mediaType = "application/pdf")),
            @ApiResponse(responseCode = "400", description = "Invalid input", content = @Content(mediaType = "application/json")),
            @ApiResponse(responseCode = "401", description = "Unauthorized", content = @Content(mediaType = "application/json"))
    })
    @PostMapping("/rearrange")
    public ResponseEntity<?> rearrangePageOrder(
            @Parameter(description = "The PDF file to rearrange", required = true)
            @RequestParam("file") MultipartFile file,

            @Parameter(description = "The new order of pages (e.g., [3, 1, 2])", required = true)
            @RequestParam("order") List<Integer> order,

            @Parameter(description = "API key for authentication", required = true)
            @RequestParam("apiKey") String apiKey,

            @Parameter(description = "Platform making the request (default: 'api')")
            @RequestParam(value = "platform", defaultValue = "api") String platform,
            HttpServletRequest request) {
        System.out.println("Received order list: " + order);
        System.out.println("File name" + file.getOriginalFilename());
        System.out.println("File size: " + file.getSize());

        ApiKey key = apiKeyRepo.findByApiKey(apiKey);
        if (key == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body("Invalid API key");
        }

        Long userId = key.getUserId();
        if (file.isEmpty() || !file.getOriginalFilename().toLowerCase().endsWith(".pdf")) {
            return ResponseEntity.badRequest().body("Invalid file. Please upload a PDF.");
        }

        try {
            PdfReader pdfReader = new PdfReader(file.getInputStream());
            int totalPages = pdfReader.getNumberOfPages();

            // Validate all page numbers in 'order' param
            if (order == null || order.size() != totalPages ||
                    order.stream().anyMatch(p -> p < 1 || p > totalPages) ||
                    new HashSet<>(order).size() != totalPages) { // check for duplicates
                pdfReader.close();
                return ResponseEntity.badRequest().body("Invalid page order list.");
            }

            Document document = new Document();
            ByteArrayOutputStream pdfRearranged = new ByteArrayOutputStream();
            PdfCopy pdfCopy = new PdfCopy(document, pdfRearranged);

            document.open();

            // Copy pages in the new order
            for (int pageNum : order) {
                PdfImportedPage importedPage = pdfCopy.getImportedPage(pdfReader, pageNum);
                pdfCopy.addPage(importedPage);
            }

            document.close();
            pdfReader.close();
            pdfCopy.close();

            byte[] rearrangedPdf = pdfRearranged.toByteArray();
            ByteArrayResource resource = new ByteArrayResource(rearrangedPdf);

            // Logging user operation (optional)
            String ip = getClientIp(request);
            Location location = locationService.getLocationFromIp(ip);
            History history = new History();
            history.setUser_id(userId);
            history.setOperation("rearrange");
            history.setTime(LocalDateTime.now());
            history.setCity(location.getCity());
            history.setState(location.getState());
            history.setPlatform(platform);
            historyRepo.save(history);

            return ResponseEntity.ok()
                    .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=rearranged.pdf")
                    .contentType(MediaType.APPLICATION_PDF)
                    .contentLength(resource.contentLength())
                    .body(resource);

        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Something went wrong: " + e.getMessage());
        }
    }


}
