<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebsiteAboutUs;
use App\Models\WebsiteContent;
use App\Models\WebsiteContactUs;

class WebsiteController extends Controller
{
    public function createHomeContent()
    {
        $heroSection = WebsiteContent::where('section', 'hero')->first();
        $secondarySection = WebsiteContent::where('section', 'secondary')->first();

        return view('website-content.home', compact('heroSection', 'secondarySection'));
    }

    public function storeHomeContent(Request $request)
    {
        $request->validate([
            'section' => 'required',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'content_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'section.required' => 'Please select a section.',
            'title.required' => 'Please enter a title.',
            'content.required' => 'Please enter the content.',
            'content_image.required' => 'Please upload an image.',
            'content_image.image' => 'The uploaded file must be an image.',
            'content_image.mimes' => 'Allowed image types are jpg, jpeg, and png.',
            'content_image.max' => 'The image size must not exceed 2MB.',
        ]);

        $imagePath = $request->file('content_image')->store('website_contents', 'public');

        $websiteContent = new WebsiteContent();
        $websiteContent->user_id = auth()->id();
        $websiteContent->section = $request->input('section');
        $websiteContent->title = $request->input('title');
        $websiteContent->content = $request->input('content');
        $websiteContent->image = $imagePath;
        $websiteContent->save();

        return redirect()->route('retailer.website-content.create')
            ->with('success', ucfirst($request->section) . ' section created successfully.');
    }

    public function updateHomeContent(Request $request, $id)
    {
        $websiteContent = WebsiteContent::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'content_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'title.required' => 'Please enter a title.',
            'content.required' => 'Please enter the content.',
            'content_image.image' => 'The uploaded file must be an image.',
            'content_image.mimes' => 'Allowed image types are jpg, jpeg, and png.',
            'content_image.max' => 'The image size must not exceed 2MB.',
        ]);

        // Update image only if new one uploaded
        if ($request->hasFile('content_image')) {
            $imagePath = $request->file('content_image')->store('website_contents', 'public');

            // Delete old image if exists
            if ($websiteContent->image && \Storage::disk('public')->exists($websiteContent->image)) {
                \Storage::disk('public')->delete($websiteContent->image);
            }

            $websiteContent->image = $imagePath;
        }

        $websiteContent->title = $request->input('title');
        $websiteContent->content = $request->input('content');
        $websiteContent->save();

        return redirect()->route('retailer.website-content.create')
            ->with('success', ucfirst($websiteContent->section) . ' section updated successfully.');
    }

    public function createAboutContent()
    {
        $aboutSection = WebsiteAboutUs::first();
        return view('website-content.about', compact('aboutSection'));
    }

    public function storeAboutContent(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'Please enter the content.',
        ]);

        $aboutUs = new WebsiteAboutUs();
        $aboutUs->user_id = auth()->id();
        $aboutUs->content = $request->input('content');
        $aboutUs->save();

        return redirect()->route('retailer.website-content.aboutus.create')
            ->with('success', 'About Us section created successfully.');
    }

    public function updateAboutContent(Request $request, $id)
    {
        $aboutUs = WebsiteAboutUs::findOrFail($id);

        $request->validate([
            'content' => 'required|string',
        ], [
            'content.required' => 'Please enter the content.',
        ]);

        $aboutUs->content = $request->input('content');
        $aboutUs->save();

        return redirect()->route('retailer.website-content.aboutus.create')
            ->with('success', 'About Us section updated successfully.');
    }

    public function createContactContent()
    {
        $contactSection = WebsiteContactUs::first();
        return view('website-content.contact', compact('contactSection'));
    }

    public function storeContactContent(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'facebook_link'  => 'nullable|url',
            'twitter_link'   => 'nullable|url',
            'linkedin_link'  => 'nullable|url',
            'instagram_link' => 'nullable|url',
        ]);

        WebsiteContactUs::create([
            'user_id'        => $request->user_id,
            'title'          => $request->title,
            'content'        => $request->content,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'facebook_link'  => $request->facebook_link,
            'twitter_link'   => $request->twitter_link,
            'linkedin_link'  => $request->linkedin_link,
            'instagram_link' => $request->instagram_link,
        ]);

        return redirect()->back()->with('success', 'Contact Us section created successfully!');
    }

    /**
     * Update Contact Us Section
     */
    public function updateContactContent(Request $request, $id)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'facebook_link'  => 'nullable|url',
            'twitter_link'   => 'nullable|url',
            'linkedin_link'  => 'nullable|url',
            'instagram_link' => 'nullable|url',
        ]);

        $contactSection = WebsiteContactUs::findOrFail($id);

        $contactSection->update([
            'title'          => $request->title,
            'content'        => $request->content,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'facebook_link'  => $request->facebook_link,
            'twitter_link'   => $request->twitter_link,
            'linkedin_link'  => $request->linkedin_link,
            'instagram_link' => $request->instagram_link,
        ]);

        return redirect()->back()->with('success', 'Contact Us section updated successfully!');
    }
}
